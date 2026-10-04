# Matriks Hak Akses Ruangsport

Role global di `users.role`: `member`, `venue_owner`, `admin`.
**Organizer bukan role global**, tetapi status kontekstual:

- *Pengelola klub* = anggota klub berstatus `approved` dengan role `owner` atau `manager`.
- *Pembuat* = `organizer_id` pada aktivitas atau kompetisi.

Cara menjadi organizer: member membuat klub, otomatis menjadi `owner` klub itu.
Aturan global (`BasePolicy`): akun nonaktif ditolak untuk semua aksi, admin diizinkan untuk semua aksi.

Legenda: ✓ boleh, ✗ tidak, ◐ boleh dengan syarat (lihat catatan).

| Aksi | Guest | Member | Pengelola klub / Pembuat | Venue Owner | Admin |
|---|:-:|:-:|:-:|:-:|:-:|
| Lihat klub, aktivitas, kompetisi, venue, komunitas | ✓ | ✓ | ✓ | ✓ | ✓ |
| Register dan login | ✓ | ✗ | ✗ | ✗ | ✗ |
| Gabung dan keluar klub | ✗ | ✓ | ✓ | ✓ | ✓ |
| RSVP dan batal RSVP aktivitas | ✗ | ✓ | ✓ | ✓ | ✓ |
| Daftar kompetisi | ✗ | ◐ a | ◐ a | ◐ a | ✓ |
| Buat tim | ✗ | ✓ | ✓ | ✓ | ✓ |
| Ubah/hapus tim | ✗ | ◐ b | ◐ b | ◐ b | ✓ |
| Posting dan komentar | ✗ | ✓ | ✓ | ✓ | ✓ |
| Hapus posting/komentar | ✗ | ◐ c | ◐ c | ◐ c | ✓ |
| Buat klub | ✗ | ✓ | ✓ | ✓ | ✓ |
| Ubah klub, kelola anggota | ✗ | ✗ | ◐ d | ✗ | ✓ |
| Hapus klub | ✗ | ✗ | ◐ e | ✗ | ✓ |
| Buat aktivitas / kompetisi | ✗ | ✗ | ✓ f | ✗ | ✓ |
| Ubah/hapus aktivitas, catat kehadiran | ✗ | ✗ | ◐ g | ✗ | ✓ |
| Ubah/hapus kompetisi, verifikasi peserta, bracket, skor | ✗ | ✗ | ◐ g | ✗ | ✓ |
| Buat venue | ✗ | ✗ | ✗ | ✓ | ✓ |
| Ubah/hapus venue | ✗ | ✗ | ✗ | ◐ h | ✓ |
| Ajukan verifikasi (klub/venue/organizer) | ✗ | ✗ | ✓ | ✓ | ✓ |
| Tinjau verifikasi, moderasi, dashboard admin | ✗ | ✗ | ✗ | ✗ | ✓ |

Catatan:

- **a.** Hanya saat status kompetisi `registration_open` dan dalam rentang waktu pendaftaran.
- **b.** Pembuat tim atau kapten untuk ubah; hanya pembuat untuk hapus.
- **c.** Penulisnya sendiri; pengelola klub boleh menghapus di feed klubnya (moderasi).
- **d.** Hanya owner/manager klub tersebut.
- **e.** Hanya owner klub.
- **f.** Syarat: pengguna mengelola setidaknya satu klub.
- **g.** Hanya pembuatnya, atau owner/manager klub penyelenggara.
- **h.** Hanya pemilik venue itu (`owner_id`).

## Pemetaan ke kode

| Policy | Ability |
|---|---|
| `ClubPolicy` | create, update, manageMembers, delete |
| `ActivityPolicy` | create, update, delete, manageParticipants |
| `CompetitionPolicy` | create, update, delete, manageEntries, manageMatches, register |
| `VenuePolicy` | create, update, delete |
| `TeamPolicy` | create, update, delete |
| `PostPolicy` / `CommentPolicy` | create, delete |

Akses panel per area dijaga middleware route: `role:admin`, `role:venue_owner,admin`.
