<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;

final readonly class SeasonService
{
    public function all(): Collection
    {
        $seasons = Season::orderBy('year', 'asc')
            ->with([
                'clubs' => function ($query) {
                    $query->orderBy('name', 'asc');
                },
            ])
            ->get();

        return $seasons;
    }

    public function find(int $id): ?Season
    {
        $season = Season::where('id', $id)
            ->with([
                'clubs' => function ($query) {
                    $query->orderBy('name', 'asc');
                },
                'rounds' => function ($query) {
                    $query->orderBy('date', 'asc');
                },
                'rounds.homeClub',
                'rounds.awayClub',
                'rounds.stadium',
            ])
            ->first();

        if ($season === null) {
            return null;
        }

        $season->loadCount('rounds');

        return $season;
    }
}
