<?php

namespace App\Support;

class Bracket
{
    /** Nama babak dihitung dari belakang: Final, Semifinal, Perempat Final, lalu Babak N. */
    public static function roundName(int $round, int $lastRound): string
    {
        return match ($lastRound - $round) {
            0 => 'Final',
            1 => 'Semifinal',
            2 => 'Perempat Final',
            default => 'Babak '.$round,
        };
    }
}
