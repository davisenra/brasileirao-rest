<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\DTOs\RoundDto;
use App\DataImporter\CsvImporter;
use Illuminate\Console\Command;

class ImportFromCsv extends Command
{
    protected $signature = 'app:import-from-csv {file}';
    protected $description = 'Import data from CSV files';

    public function __construct(private readonly CsvImporter $csvImporter)
    {
        parent::__construct();
    }

    public function handle()
    {
        $file = $this->argument('file');
        $this->info("Importing from $file");

        try {
            $file = new \SplFileObject($file, 'r');
        } catch (\RuntimeException $e) {
            $this->error("Cannot open $file");
            return;
        }

        $this->info("Parsing CSV file");
        $totalRows = $this->getRowsCount($file);
        $bar = $this->output->createProgressBar($totalRows);
        $bar->start();

        $file->fgetcsv(); // discard header

        $roundsDtos = [];

        while ($file->valid()) {
            $row = $file->fgetcsv();
            $malformedRow = is_null($row[0]);

            if ($malformedRow) {
                continue;
            };

            $stadiumName = preg_replace('/\([^)]+\)/', '', $row[11]); // remove parenthesis
            $stadiumName = preg_replace('/^\x{A0}/u', '', $stadiumName); // remove non-breaking space
            $stadiumName = str_replace(['*'], '', $stadiumName);

            $roundsDtos[] = new RoundDto(
                matchDate: \DateTime::createFromFormat('d/m/Y H:i', "$row[2] $row[3]"),
                stadiumName: trim($stadiumName),
                homeClubName: $row[4],
                awayClubName: $row[5],
                homeClubScore: $row[12] ? (int) $row[12] : 0,
                awayClubScore: $row[13] ? (int) $row[13] : 0,
                roundNumber: (int) $row[1],
                homeClubState: $row[14],
                awayClubState: $row[15],
            );

            $bar->advance();
        }

        $file = null; // free memory
        $bar->finish();
        unset($bar);

        $bar = $this->output->createProgressBar(4);

        $this->info("\nPersisting data");

        $bar->start();
        $this->csvImporter->importClubs($roundsDtos);
        $bar->advance();
        $this->csvImporter->importStadiums($roundsDtos);
        $bar->advance();
        $this->csvImporter->importSeasons($roundsDtos);
        $bar->advance();
        $this->csvImporter->importRounds($roundsDtos);
        $bar->advance();
        $bar->finish();

        $this->info("\nDone");
    }

    private function getRowsCount(\SplFileObject $file): int
    {
        $file->seek(PHP_INT_MAX);
        $rowsCount = $file->key();
        $file->rewind();

        return $rowsCount;
    }
}
