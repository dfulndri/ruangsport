<?php

namespace App\Enums;

enum ClubMemberRole: string
{
    case Owner = 'owner';
    case Manager = 'manager';
    case Member = 'member';

    public function canManage(): bool
    {
        return in_array($this, [self::Owner, self::Manager], true);
    }
}
