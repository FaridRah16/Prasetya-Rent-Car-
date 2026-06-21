# Spesifikasi Database

Spesifikasi basis data (kamus data) aplikasi **Prasetya Rent Car** berdasarkan
[Logical Record Structure](09-logical-record-structure.md) dan file migration. Tiap
tabel dirinci: nama, primary key, foreign key, dan daftar field beserta tipe,
panjang, null, dan keterangan.

## Informasi Umum

| Properti | Nilai |
|----------|-------|
| Nama Database | `prasetya_rentcar` |
| DBMS | MySQL / MariaDB |
| Storage Engine | InnoDB (mendukung foreign key) |
| Character Set | `utf8mb4` |
| Collation | `utf8mb4_unicode_ci` |
| Jumlah Tabel Domain | 5 (`users`, `cars`, `drivers`, `bookings`, `reviews`) |
| Pola Primary Key | Surrogate key `id` `BIGINT UNSIGNED AUTO_INCREMENT` |
| Timestamps | `created_at`, `updated_at` (nullable) di semua tabel |

---

## 1. Tabel `users`

- **Primary Key:** `id`
- **Foreign Key:** —
- **Unique:** `email`
- **Fungsi:** menyimpan akun pengguna (admin, customer, driver) sekaligus data verifikasi.

| No | Field | Tipe | Panjang | Null | Default | Key | Keterangan |
|----|-------|------|---------|------|---------|-----|------------|
| 1 | id | bigint unsigned | — | Tidak | auto | PK | Identitas user |
| 2 | name | varchar | 255 | Tidak | — | | Nama lengkap |
| 3 | email | varchar | 255 | Tidak | — | UQ | Email login |
| 4 | email_verified_at | timestamp | — | Ya | NULL | | Verifikasi email (bawaan Laravel) |
| 5 | password | varchar | 255 | Tidak | — | | Hash password |
| 6 | role | enum | admin/customer/driver | Tidak | customer | | Peran pengguna |
| 7 | verification_status | enum | unverified/pending/verified | Tidak | unverified | | Status verifikasi akun |
| 8 | phone | varchar | 255 | Ya | NULL | | Nomor telepon |
| 9 | whatsapp_number | varchar | 20 | Ya | NULL | | Nomor WhatsApp |
| 10 | avatar | varchar | 255 | Ya | NULL | | Path foto profil |
| 11 | sim_photo | varchar | 255 | Ya | NULL | | Path foto SIM (verifikasi) |
| 12 | verified_at | timestamp | — | Ya | NULL | | Waktu diverifikasi admin |
| 13 | remember_token | varchar | 100 | Ya | NULL | | Token "remember me" |
| 14 | created_at | timestamp | — | Ya | NULL | | Waktu dibuat |
| 15 | updated_at | timestamp | — | Ya | NULL | | Waktu diperbarui |

---

## 2. Tabel `cars`

- **Primary Key:** `id`
- **Foreign Key:** —
- **Unique:** `plate_number`
- **Fungsi:** data mobil yang disewakan.

| No | Field | Tipe | Panjang | Null | Default | Key | Keterangan |
|----|-------|------|---------|------|---------|-----|------------|
| 1 | id | bigint unsigned | — | Tidak | auto | PK | Identitas mobil |
| 2 | name | varchar | 255 | Tidak | — | | Nama mobil |
| 3 | brand | varchar | 255 | Tidak | — | | Merek |
| 4 | type | varchar | 255 | Tidak | — | | Jenis (SUV, MPV, dll) |
| 5 | year | year | — | Tidak | — | | Tahun produksi |
| 6 | color | varchar | 255 | Tidak | — | | Warna |
| 7 | plate_number | varchar | 255 | Tidak | — | UQ | Plat nomor |
| 8 | price_per_day | decimal | 10,2 | Tidak | — | | Harga sewa per hari |
| 9 | status | enum | available/rented/maintenance | Tidak | available | | Status mobil |
| 10 | image | varchar | 255 | Ya | NULL | | Foto utama |
| 11 | gallery | text | — | Ya | NULL | | JSON array path foto (cast array) |
| 12 | seats | int | — | Tidak | — | | Jumlah kursi |
| 13 | description | text | — | Ya | NULL | | Deskripsi |
| 14 | created_at | timestamp | — | Ya | NULL | | Waktu dibuat |
| 15 | updated_at | timestamp | — | Ya | NULL | | Waktu diperbarui |

---

## 3. Tabel `drivers`

- **Primary Key:** `id`
- **Foreign Key:** `user_id` → `users.id` (ON DELETE CASCADE)
- **Unique:** `license_number`
- **Fungsi:** profil driver, terhubung 1:1 ke akun user berperan `driver`.

| No | Field | Tipe | Panjang | Null | Default | Key | Keterangan |
|----|-------|------|---------|------|---------|-----|------------|
| 1 | id | bigint unsigned | — | Tidak | auto | PK | Identitas driver |
| 2 | user_id | bigint unsigned | — | Tidak | — | FK | → `users.id` (cascade) |
| 3 | license_number | varchar | 255 | Tidak | — | UQ | Nomor SIM |
| 4 | status | enum | available/on_duty | Tidak | available | | Status driver |
| 5 | created_at | timestamp | — | Ya | NULL | | Waktu dibuat |
| 6 | updated_at | timestamp | — | Ya | NULL | | Waktu diperbarui |

---

## 4. Tabel `bookings`

- **Primary Key:** `id`
- **Foreign Key:** `user_id` → `users.id` (CASCADE), `car_id` → `cars.id` (CASCADE), `driver_id` → `users.id` (SET NULL)
- **Fungsi:** transaksi penyewaan mobil.

| No | Field | Tipe | Panjang | Null | Default | Key | Keterangan |
|----|-------|------|---------|------|---------|-----|------------|
| 1 | id | bigint unsigned | — | Tidak | auto | PK | Identitas booking |
| 2 | user_id | bigint unsigned | — | Tidak | — | FK | Pemesan → `users.id` (cascade) |
| 3 | car_id | bigint unsigned | — | Tidak | — | FK | Mobil → `cars.id` (cascade) |
| 4 | driver_id | bigint unsigned | — | Ya | NULL | FK | Driver → `users.id` (set null) |
| 5 | start_date | date | — | Tidak | — | | Tanggal mulai sewa |
| 6 | pickup_time | time | — | Ya | NULL | | Jam penjemputan |
| 7 | end_date | date | — | Tidak | — | | Tanggal selesai sewa |
| 8 | return_time | time | — | Ya | NULL | | Jam pengembalian |
| 9 | total_days | int | — | Tidak | — | | Total hari (ceil jam/24, min 1) |
| 10 | total_price | decimal | 10,2 | Tidak | — | | total_days × price_per_day |
| 11 | pickup_location | varchar | 255 | Tidak | — | | Lokasi penjemputan |
| 12 | pickup_lat | decimal | 10,7 | Ya | NULL | | Koordinat lat penjemputan |
| 13 | pickup_lng | decimal | 10,7 | Ya | NULL | | Koordinat lng penjemputan |
| 14 | dropoff_location | varchar | 255 | Tidak | — | | Lokasi pengantaran |
| 15 | dropoff_lat | decimal | 10,7 | Ya | NULL | | Koordinat lat pengantaran |
| 16 | dropoff_lng | decimal | 10,7 | Ya | NULL | | Koordinat lng pengantaran |
| 17 | status | enum | pending/confirmed/ongoing/completed/cancelled | Tidak | pending | | Status booking |
| 18 | payment_status | enum | unpaid/paid | Tidak | unpaid | | Status pembayaran |
| 19 | payment_proof | varchar | 255 | Ya | NULL | | Path bukti pembayaran |
| 20 | delivery_proof | varchar | 255 | Ya | NULL | | Path bukti pengantaran (driver) |
| 21 | notes | text | — | Ya | NULL | | Catatan |
| 22 | created_at | timestamp | — | Ya | NULL | | Waktu dibuat |
| 23 | updated_at | timestamp | — | Ya | NULL | | Waktu diperbarui |

---

## 5. Tabel `reviews`

- **Primary Key:** `id`
- **Foreign Key:** `booking_id` → `bookings.id` (CASCADE), `user_id` → `users.id` (CASCADE)
- **Fungsi:** ulasan & rating customer terhadap booking.

| No | Field | Tipe | Panjang | Null | Default | Key | Keterangan |
|----|-------|------|---------|------|---------|-----|------------|
| 1 | id | bigint unsigned | — | Tidak | auto | PK | Identitas review |
| 2 | booking_id | bigint unsigned | — | Tidak | — | FK | → `bookings.id` (cascade) |
| 3 | user_id | bigint unsigned | — | Tidak | — | FK | → `users.id` (cascade) |
| 4 | rating | int unsigned | — | Tidak | — | | Nilai rating |
| 5 | comment | text | — | Ya | NULL | | Komentar |
| 6 | created_at | timestamp | — | Ya | NULL | | Waktu dibuat |
| 7 | updated_at | timestamp | — | Ya | NULL | | Waktu diperbarui |

---

## Tabel Pendukung Framework

Tabel berikut dibuat Laravel namun bukan bagian domain bisnis:

| Tabel | Primary Key | Fungsi |
|-------|-------------|--------|
| `password_reset_tokens` | `email` | Token reset password |
| `sessions` | `id` | Penyimpanan session (driver `database`) |
| `cache`, `cache_locks` | `key` | Cache driver database |
| `jobs` | `id` | Antrian job |
| `job_batches` | `id` | Batch job |
| `failed_jobs` | `id` | Job gagal |

> Keterangan: `UQ` = Unique, `PK` = Primary Key, `FK` = Foreign Key. Kolom `Panjang`
> diisi panjang karakter (varchar), presisi,skala (decimal), atau daftar nilai (enum)
> sesuai konteks.
