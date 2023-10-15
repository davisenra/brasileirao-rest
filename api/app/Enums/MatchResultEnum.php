<?php

declare(strict_types=1);

namespace App\Enums;

enum MatchResultEnum: int
{
    case DRAW = 0;
    case HOME_WIN = 1;
    case AWAY_WIN = 2;

    public static function fromScores(int $homeScore, int $awayScore): self
    {
        if ($homeScore === $awayScore) {
            return self::DRAW;
        }

        if ($homeScore > $awayScore) {
            return self::HOME_WIN;
        }

        return self::AWAY_WIN;
    }
}
