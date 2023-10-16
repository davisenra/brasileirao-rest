<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Club;
use Illuminate\Database\Eloquent\Collection;

final readonly class ClubService
{
    public function all(): Collection
    {
        $clubs = Club::orderBy('name')->get();

        return $clubs;
    }

    public function find(int $id): ?Club
    {
        $club = Club::where('id', $id)
            ->with([
                'seasons' => function ($query) {
                    $query->orderBy('year', 'asc');
                },
            ])
            ->first();

        if ($club === null) {
            return null;
        }

        return $club;
    }
}
