<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case Closed = 'closed';
    case Finished = 'finished';
    case Cancelled = 'cancelled';
}
