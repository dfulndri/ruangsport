<?php

namespace App\Support;

use BackedEnum;
use Illuminate\Support\HtmlString;

/**
 * Teks dan warna badge untuk nilai enum, supaya view tidak berisi array label berulang.
 * Pemakaian di Blade: {{ \App\Support\Label::badge('activity', $activity->status) }}
 */
class Label
{
    private const MAP = [
        'activity' => [
            'draft' => ['Draf', 'secondary'], 'open' => ['Dibuka', 'success'], 'closed' => ['Ditutup', 'warning'],
            'finished' => ['Selesai', 'info'], 'cancelled' => ['Dibatalkan', 'danger'],
        ],
        'competition' => [
            'draft' => ['Draf', 'secondary'], 'registration_open' => ['Pendaftaran dibuka', 'success'],
            'ongoing' => ['Berlangsung', 'primary'], 'finished' => ['Selesai', 'info'],
        ],
        'entry' => [
            'pending' => ['Menunggu', 'warning'], 'verified' => ['Terverifikasi', 'success'],
            'rejected' => ['Ditolak', 'danger'], 'withdrawn' => ['Mengundurkan diri', 'secondary'],
        ],
        'member' => [
            'pending' => ['Menunggu', 'warning'], 'approved' => ['Disetujui', 'success'], 'rejected' => ['Ditolak', 'danger'],
        ],
        'participant' => [
            'registered' => ['Terdaftar', 'success'], 'waitlisted' => ['Daftar tunggu', 'warning'],
            'cancelled' => ['Dibatalkan', 'secondary'], 'attended' => ['Hadir', 'primary'], 'no_show' => ['Tidak hadir', 'danger'],
        ],
        'verification' => [
            'pending' => ['Menunggu', 'warning'], 'approved' => ['Disetujui', 'success'], 'rejected' => ['Ditolak', 'danger'],
        ],
        'club_role' => ['owner' => ['Pemilik', 'dark'], 'manager' => ['Pengelola', 'info'], 'member' => ['Anggota', 'light']],
        'user_role' => ['member' => ['Member', 'light'], 'venue_owner' => ['Pemilik venue', 'info'], 'admin' => ['Admin', 'dark']],
        'participation' => ['individual' => ['Individu', 'light'], 'team' => ['Tim', 'light'], 'both' => ['Individu & tim', 'light']],
    ];

    public static function text(string $type, mixed $value): string
    {
        $key = $value instanceof BackedEnum ? $value->value : (string) $value;

        return self::MAP[$type][$key][0] ?? $key;
    }

    public static function badge(string $type, mixed $value): HtmlString
    {
        $key = $value instanceof BackedEnum ? $value->value : (string) $value;
        [$text, $color] = self::MAP[$type][$key] ?? [$key, 'secondary'];

        return new HtmlString('<span class="badge badge-'.e($color).'">'.e($text).'</span>');
    }
}
