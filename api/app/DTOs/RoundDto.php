<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class RoundDto
{
    public function __construct(
        public \DateTime $matchDate,
        public string $stadiumName,
        public string $homeClubName,
        public string $awayClubName,
        public int $homeClubScore,
        public int $awayClubScore,
        public int $roundNumber,
        public string $homeClubState,
        public string $awayClubState,
    ) {
    }
}
