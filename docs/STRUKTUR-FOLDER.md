# Struktur Folder Ruangsport

Aturan dasar: **satu area = satu folder dengan nama yang sama di setiap lapisan**.
Kalau mau menambah fitur untuk area "Organizer", cari folder `Organizer` di controller,
request, route, dan view.

```
ruangsport/
├── docker/ , docker-compose.yml     Lingkungan Docker (PHP, Nginx, MySQL)
├── tamplate-ruangsport/             Template asli Stamina (jangan diubah, hanya sumber salinan)
├── docs/                            Dokumen proyek (file ini, matriks akses, langkah)
└── src/                             Aplikasi Laravel
    ├── app/
    │   ├── Enums/                   Nilai tetap (status, role). Satu enum per konsep.
    │   ├── Models/                  Model Eloquent + relasi (tanpa logika bisnis berat)
    │   ├── Policies/                Hak akses per model (siapa boleh apa)
    │   ├── Services/                Logika bisnis, dikelompokkan per modul
    │   │   ├── Activity/            RSVP, waiting list
    │   │   ├── Club/                bergabung, persetujuan anggota
    │   │   ├── Competition/         pembuatan bracket, input skor
    │   │   ├── Venue/
    │   │   └── Verification/        pengajuan dan peninjauan verifikasi
    │   ├── Http/
    │   │   ├── Controllers/         Tipis: validasi, panggil Service, kembalikan view
    │   │   │   ├── Site/            Halaman publik (guest boleh)
    │   │   │   ├── Auth/            Login, register, logout
    │   │   │   ├── Member/          Aksi member yang sudah login
    │   │   │   ├── Organizer/       Kelola klub, kegiatan, kompetisi, bracket
    │   │   │   ├── VenueOwner/      Kelola venue
    │   │   │   └── Admin/           Verifikasi, moderasi, dashboard
    │   │   ├── Requests/            Form Request (validasi), subfolder sama dengan Controllers
    │   │   └── Middleware/          EnsureUserHasRole (alias: role:admin)
    │   ├── Support/                 Helper kecil (Placeholder, Format, Slug, Label, Bracket)
    │   └── View/Composers/          Data bersama untuk view tertentu
    ├── database/
    │   ├── migrations/              Skema (urut berdasarkan tanggal)
    │   └── seeders/                 SportSeeder, LocationSeeder, DemoSeeder
    ├── routes/
    │   ├── web.php                  Hanya memuat file di routes/web/
    │   └── web/                     site.php, auth.php, member.php, admin.php,
    │                                organizer.php, venue-owner.php
    ├── resources/views/
    │   ├── layouts/app.blade.php    Layout utama (dari template Stamina)
    │   ├── partials/                Potongan dipakai ulang (navbar, footer, kartu)
    │   ├── pages/ clubs/ activities/ competitions/ venues/ community/   Halaman publik
    │   ├── auth/ member/ admin/     Halaman login & dashboard
    │   └── organizer/ venue-owner/  Panel pengelola
    └── public/assets/ruangsport/    Aset template (css, js, fonts, images) + custom.css
```

## Aturan penamaan

| Hal | Aturan | Contoh |
|---|---|---|
| Controller | `<Modul>Controller`, di folder area | `Organizer/ActivityController` |
| Form Request | `<Aksi><Modul>Request`, folder area | `Organizer/StoreActivityRequest` |
| Service | `<Modul>Service` atau `<Aksi>Service` | `Competition/BracketGenerator` |
| Policy | `<Model>Policy` (otomatis terdeteksi Laravel) | `ActivityPolicy` |
| Route name | `<area>.<modul>.<aksi>` | `organizer.activities.store` |
| View | `resources/views/<area>/<modul>/<aksi>.blade.php` | `organizer/activities/create.blade.php` |

## Alur satu fitur (urutan menulis kode)

1. Route di `routes/web/<area>.php`
2. Form Request untuk validasi
3. Controller tipis: `Gate::authorize('update', $model)`, panggil Service, redirect
4. Service untuk logika bisnis (transaksi database di sini)
5. View di `resources/views/<area>/...`

## Hak akses di controller

Controller dasar Laravel 11+ tidak punya `authorize()`. Pakai facade:

```php
use Illuminate\Support\Facades\Gate;

Gate::authorize('update', $club);   // melempar 403 jika tidak boleh
```

Di Blade: `@can('update', $club) ... @endcan`
