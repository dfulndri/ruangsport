<?php

namespace App\Enums;

enum ClubMemberStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
