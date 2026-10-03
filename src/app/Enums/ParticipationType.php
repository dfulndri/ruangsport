<?php

namespace App\Enums;

/** Jenis peserta: dipakai pada sports (yang diizinkan) dan competitions (yang dipilih). */
enum ParticipationType: string
{
    case Individual = 'individual';
    case Team = 'team';
    case Both = 'both'; // hanya valid untuk sports.participation_type
}
