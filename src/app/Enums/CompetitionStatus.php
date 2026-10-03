<?php

namespace App\Enums;

enum CompetitionStatus: string
{
    case Draft = 'draft';
    case RegistrationOpen = 'registration_open';
    case Ongoing = 'ongoing';
    case Finished = 'finished';
}
