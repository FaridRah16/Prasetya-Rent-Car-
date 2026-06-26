# Dokumentasi UML — Prasetya Rent Car

Dokumentasi diagram UML lengkap untuk aplikasi **Prasetya Rent Car** (Laravel 12).
Semua diagram dibuat dengan **Mermaid** sehingga dapat langsung dirender di GitHub,
VS Code (ekstensi Mermaid), atau editor lain yang mendukung Mermaid.

## Tentang Sistem

Aplikasi penyewaan mobil (car rental) berbasis web dengan 3 peran pengguna:

| Peran        | Hak Akses Utama                                                                 |
|--------------|---------------------------------------------------------------------------------|
| **Admin**    | Kelola mobil, kelola user/driver, verifikasi pembayaran, kelola booking, laporan |
| **Customer** | Lihat mobil, buat booking, bayar (Midtrans/transfer manual), batalkan booking, profil |
| **Driver**   | Lihat tugas, mulai tugas, upload bukti pengantaran, riwayat tugas                |

Stack teknologi: **Laravel 12**, PHP 8.3, Blade, MySQL, autentikasi session-based dengan
`RoleMiddleware` untuk otorisasi berbasis peran, dan **Midtrans Snap** sebagai payment
gateway (mode sandbox).

## Tentang Pembayaran

Sistem mendukung **dua jalur pembayaran**:

1. **Online via Midtrans Snap** (utama) — customer membayar lewat halaman Snap
   (bank transfer/VA, GoPay, QRIS, dll). Status disinkronkan via webhook
   (`/api/payment/notification`) maupun pengecekan manual ke Midtrans API.
2. **Transfer manual + upload bukti** (alternatif) — customer mengunggah bukti
   transfer, lalu admin memverifikasi.

Booking berstatus `pending` yang belum dibayar akan **dibatalkan otomatis** setelah
melewati batas waktu pembayaran (`payment_window_minutes`, default 30 menit) lewat
command terjadwal `bookings:expire-pending` dan mekanisme *lazy-expire*.

## Daftar Diagram

| No | Dokumen | Isi |
|----|---------|-----|
| 1 | [01-erd.md](01-erd.md) | Entity Relationship Diagram (ERD) |
| 2 | [02-relasi-tabel.md](02-relasi-tabel.md) | Relasi antar tabel & kardinalitas |
| 3 | [03-struktur-tabel.md](03-struktur-tabel.md) | Struktur kolom setiap tabel |
| 4 | [04-use-case-diagram.md](04-use-case-diagram.md) | Use Case Diagram per aktor |
| 5 | [05-activity-diagram.md](05-activity-diagram.md) | Activity Diagram alur utama |
| 6 | [06-sequence-diagram.md](06-sequence-diagram.md) | Sequence Diagram interaksi sistem |
| 7 | [07-class-diagram.md](07-class-diagram.md) | Class Diagram (Model, Controller, Service) |
| 8 | [08-transformasi-erd-lrs.md](08-transformasi-erd-lrs.md) | Transformasi ERD ke Logical Record Structure |
| 9 | [09-logical-record-structure.md](09-logical-record-structure.md) | Logical Record Structure (LRS) |
| 10 | [10-spesifikasi-database.md](10-spesifikasi-database.md) | Spesifikasi Database (kamus data) |

## Ringkasan Entitas

- **User** — akun pengguna (admin / customer / driver) sekaligus data verifikasi akun
- **Car** — data mobil yang disewakan (termasuk transmisi & bahan bakar)
- **Driver** — profil driver (terhubung 1:1 ke User berperan driver)
- **Booking** — transaksi penyewaan mobil, termasuk data pembayaran Midtrans

> Catatan: fitur **Review** sudah **dihapus** dari sistem (tabel `reviews` di-drop
> lewat migration `2026_06_21_140000_drop_reviews_table`). Domain bisnis kini terdiri
> dari **4 entitas**.
