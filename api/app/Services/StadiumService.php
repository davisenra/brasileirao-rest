<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Stadium;
use Illuminate\Database\Eloquent\Collection;

final readonly class StadiumService
{
    /**
     * @return Collection<Stadium>
     */
    public function all(): Collection
    {
        $stadiums = Stadium::all();
        $stadiums->loadCount('rounds');

        return $stadiums;
    }

    public function find(int $id): ?Stadium
    {
        $stadium = Stadium::where('id', $id)
            ->with([
                'rounds' => function ($query) {
                    $query->orderBy('date', 'asc');
                },
                'rounds.homeClub',
                'rounds.awayClub',
                'rounds.season',
            ])
            ->first();

        if ($stadium === null) {
            return null;
        }

        $stadium->loadCount('rounds');

        return $stadium;
    }
}
