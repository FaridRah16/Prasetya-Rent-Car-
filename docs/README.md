# Dokumentasi UML — Prasetya Rent Car

Dokumentasi diagram UML lengkap untuk aplikasi **Prasetya Rent Car** (Laravel 13).
Semua diagram dibuat dengan **Mermaid** sehingga dapat langsung dirender di GitHub,
VS Code (ekstensi Mermaid), atau editor lain yang mendukung Mermaid.

## Tentang Sistem

Aplikasi penyewaan mobil (car rental) berbasis web dengan 3 peran pengguna:

| Peran        | Hak Akses Utama                                                                 |
|--------------|---------------------------------------------------------------------------------|
| **Admin**    | Kelola mobil, kelola user/driver, verifikasi pembayaran, kelola booking, laporan |
| **Customer** | Lihat mobil, buat booking, upload bukti bayar, batalkan booking, profil          |
| **Driver**   | Lihat tugas, mulai tugas, upload bukti pengantaran, riwayat tugas                |

Stack teknologi: Laravel 13, PHP 8.3, Blade, MySQL, autentikasi session-based dengan
`RoleMiddleware` untuk otorisasi berbasis peran.

## Daftar Diagram

| No | Dokumen | Isi |
|----|---------|-----|
| 1 | [01-erd.md](01-erd.md) | Entity Relationship Diagram (ERD) |
| 2 | [02-relasi-tabel.md](02-relasi-tabel.md) | Relasi antar tabel & kardinalitas |
| 3 | [03-struktur-tabel.md](03-struktur-tabel.md) | Struktur kolom setiap tabel |
| 4 | [04-use-case-diagram.md](04-use-case-diagram.md) | Use Case Diagram per aktor |
| 5 | [05-activity-diagram.md](05-activity-diagram.md) | Activity Diagram alur utama |
| 6 | [06-sequence-diagram.md](06-sequence-diagram.md) | Sequence Diagram interaksi sistem |
| 7 | [07-class-diagram.md](07-class-diagram.md) | Class Diagram (Model & Controller) |
| 8 | [08-transformasi-erd-lrs.md](08-transformasi-erd-lrs.md) | Transformasi ERD ke Logical Record Structure |
| 9 | [09-logical-record-structure.md](09-logical-record-structure.md) | Logical Record Structure (LRS) |
| 10 | [10-spesifikasi-database.md](10-spesifikasi-database.md) | Spesifikasi Database (kamus data) |

## Ringkasan Entitas

- **User** — akun pengguna (admin / customer / driver)
- **Car** — data mobil yang disewakan
- **Driver** — profil driver (terhubung 1:1 ke User berperan driver)
- **Booking** — transaksi penyewaan mobil
- **Review** — ulasan & rating customer terhadap booking
