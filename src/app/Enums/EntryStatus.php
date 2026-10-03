<?php

namespace App\Enums;

enum EntryStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';
}
