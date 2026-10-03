<?php

namespace App\Enums;

enum MatchStatus: string
{
    case Scheduled = 'scheduled';
    case Ongoing = 'ongoing';
    case Finished = 'finished';
    case Walkover = 'walkover';
}
