<?php

declare(strict_types=1);

namespace App\DataImporter;

use App\Models\Club;
use App\Models\Round;
use App\DTOs\RoundDto;
use App\Models\Season;
use App\Models\Stadium;
use App\Enums\MatchResultEnum;
use Carbon\Carbon;

final class CsvImporter
{
    /** @var Club[] $clubs */
    private array $clubs = [];

    /** @var Stadium[] $stadiums */
    private array $stadiums = [];

    /** @var Season[] $seasons */
    private array $seasons = [];

    /**
     * @param array<RoundDto> $rounds
     */
    public function importClubs($rounds): void
    {
        $clubs = [];
        $clubNames = [];

        foreach ($rounds as $round) {
            $homeClubImported = in_array($round->homeClubName, $clubNames);
            $awayClubImported = in_array($round->awayClubName, $clubNames);

            if ($homeClubImported && $awayClubImported) {
                continue;
            }

            if (!$homeClubImported) {
                $clubs[] = Club::create([
                    'name' => $round->homeClubName,
                    'state' => $round->homeClubState,
                ]);
                $clubNames[] = $round->homeClubName;
            }

            if (!$awayClubImported) {
                $clubs[] = Club::create([
                    'name' => $round->awayClubName,
                    'state' => $round->awayClubState,
                ]);
                $clubNames[] = $round->awayClubName;
            }
        }

        $this->clubs = $clubs;
    }

    /**
     * @param array<RoundDto> $rounds
     */
    public function importStadiums($rounds): void
    {
        $stadiums = [];

        foreach ($rounds as $round) {
            $stadiumAlreadyImported = array_filter(
                $stadiums,
                fn (Stadium $stadium) => $stadium->name === $round->stadiumName
            );

            if ($stadiumAlreadyImported) {
                continue;
            }

            $stadiums[] = Stadium::create([
                'name' => $round->stadiumName,
            ]);
        }

        $this->stadiums = $stadiums;
    }

    /**
     * @param array<RoundDto> $rounds
     */
    public function importSeasons($rounds): void
    {
        $seasons = [];

        foreach ($rounds as $round) {
            $pandemicSeason = $round->matchDate->format('Ymd') > '20200301' && $round->matchDate->format('Ymd') < '20210330';
            $seasonYear = $pandemicSeason ? '2020' : $round->matchDate->format('Y');
            $seasonName = "Brasileirão $seasonYear";

            $seasonAlreadyImported = array_filter(
                $seasons,
                fn (Season $season) => $season->edition_name === $seasonName
            );

            if ($seasonAlreadyImported) {
                continue;
            }

            $seasons[] = Season::create([
                'edition_name' => $seasonName,
                'year' => $seasonYear,
            ]);
        }

        $this->seasons = $seasons;

        /** @var array<string, string[]> $clubsBySeason */
        $clubsBySeason = [];

        foreach ($rounds as $round) {
            $pandemicSeason = $round->matchDate->format('Ymd') > '20200301' && $round->matchDate->format('Ymd') < '20210330';
            $seasonYear = $pandemicSeason ? '2020' : $round->matchDate->format('Y');

            if (!isset($clubsBySeason[$seasonYear])) {
                $clubsBySeason[$seasonYear] = [];
            }

            if (!in_array($round->awayClubName, $clubsBySeason[$seasonYear])) {
                $clubsBySeason[$seasonYear][] = $round->awayClubName;
            }

            if (!in_array($round->homeClubName, $clubsBySeason[$seasonYear])) {
                $clubsBySeason[$seasonYear][] = $round->homeClubName;
            }
        }

        foreach ($seasons as $season) {
            $clubsParticipating = array_filter(
                $this->clubs,
                fn (Club $club) => in_array($club->name, $clubsBySeason[$season->year])
            );

            $clubsIds = array_map(
                fn (Club $club) => $club->id,
                $clubsParticipating
            );

            $season->clubs()->sync($clubsIds);
        }
    }

    /**
     * @param array<RoundDto> $rounds
     */
    public function importRounds($rounds): void
    {
        foreach ($rounds as $roundDto) {
            $pandemicSeason = $roundDto->matchDate->format('Ymd') > '20200301' && $roundDto->matchDate->format('Ymd') < '20210330';
            $seasonYear = $pandemicSeason ? '2020' : $roundDto->matchDate->format('Y');
            $seasonName = "Brasileirão $seasonYear";

            $season = array_values(array_filter(
                $this->seasons,
                fn (Season $season) => $season->edition_name === $seasonName
            ))[0];

            $stadium = array_values(array_filter(
                $this->stadiums,
                fn (Stadium $stadium) => $stadium->name === $roundDto->stadiumName
            ))[0];

            $homeClub = array_values(array_filter(
                $this->clubs,
                fn (Club $club) => $club->name === $roundDto->homeClubName
            ))[0];

            $awayClub = array_values(array_filter(
                $this->clubs,
                fn (Club $club) => $club->name === $roundDto->awayClubName
            ))[0];

            $round = new Round();
            $round->date = new Carbon($roundDto->matchDate);
            $round->stadium_id = $stadium->id;
            $round->season_id = $season->id;
            $round->home_club_id = $homeClub->id;
            $round->away_club_id = $awayClub->id;
            $round->home_club_score = $roundDto->homeClubScore;
            $round->away_club_score = $roundDto->awayClubScore;
            $round->round_number = $roundDto->roundNumber;
            $round->result = MatchResultEnum::fromScores($roundDto->homeClubScore, $roundDto->awayClubScore);
            $round->save();

            if ($roundDto->roundNumber === 1 && $season->first_round === null) {
                $season->first_round = $roundDto->matchDate->format('Y-m-d H:i:s');
                $season->update();
            }

            $maxRounds = match ($seasonYear) {
                '2003' => 46,
                '2004' => 46,
                '2005' => 42,
                default => 38,
            };

            if ($roundDto->roundNumber === $maxRounds) {
                $season->last_round = $roundDto->matchDate->format('Y-m-d H:i:s');
                $season->update();
            }
        }
    }
}
