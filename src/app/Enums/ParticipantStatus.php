<?php

namespace App\Enums;

enum ParticipantStatus: string
{
    case Registered = 'registered';
    case Waitlisted = 'waitlisted';
    case Cancelled = 'cancelled';
    case Attended = 'attended';
    case NoShow = 'no_show';
}
