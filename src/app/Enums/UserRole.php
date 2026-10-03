<?php

namespace App\Enums;

enum UserRole: string
{
    case Member = 'member';
    case VenueOwner = 'venue_owner';
    case Admin = 'admin';
}
