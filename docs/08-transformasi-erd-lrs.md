# Transformasi ERD ke Logical Record Structure (LRS)

Dokumen ini menjelaskan langkah transformasi dari **Entity Relationship Diagram**
([01-erd.md](01-erd.md)) menjadi **Logical Record Structure (LRS)**
([09-logical-record-structure.md](09-logical-record-structure.md)). Transformasi
mengubah model konseptual (entitas–relasi) menjadi model logis berupa kumpulan
*record* (tabel) yang siap diimplementasikan pada DBMS relasional.

## Aturan Transformasi ERD → LRS

Transformasi mengikuti kaidah baku pemetaan relasi menjadi record:

| No | Derajat Relasi | Aturan Penempatan |
|----|----------------|-------------------|
| 1 | **One-to-One (1:1)** | Foreign key ditempatkan pada salah satu record (umumnya record yang keberadaannya bergantung / opsional). |
| 2 | **One-to-Many (1:N)** | Foreign key ditempatkan pada record di sisi **Many**. |
| 3 | **Many-to-Many (M:N)** | Dibentuk record/tabel baru (penghubung) yang memuat primary key kedua entitas sebagai foreign key. |
| 4 | **Atribut multivalue** | Dipisah menjadi record tersendiri **atau** disimpan sebagai struktur tertanam (mis. JSON). |

## Identifikasi Relasi pada ERD

Berdasarkan ERD Prasetya Rent Car terdapat **4 relasi**. Tidak ada relasi M:N,
sehingga **tidak terbentuk tabel penghubung baru** — jumlah record pada LRS sama
dengan jumlah entitas (**4 record**).

| Relasi | Derajat | Aturan | Hasil Penempatan FK |
|--------|---------|--------|---------------------|
| User – Driver (memiliki profil) | 1 : 1 | Aturan 1 | `user_id` pada record **drivers** |
| User – Booking (membuat) | 1 : N | Aturan 2 | `user_id` pada record **bookings** |
| User – Booking (ditugaskan sbg driver) | 1 : N | Aturan 2 | `driver_id` pada record **bookings** (→ `users.id`) |
| Car – Booking (disewa) | 1 : N | Aturan 2 | `car_id` pada record **bookings** |

> **Catatan:** relasi *User ditugaskan sebagai driver* memetakan `bookings.driver_id`
> ke **`users.id`** (akun berperan `driver`), bukan ke `drivers.id`. Profil pada record
> `drivers` tetap terhubung ke `users` lewat `drivers.user_id`.

## Penanganan Atribut Khusus

| Atribut | Entitas | Penanganan |
|---------|---------|-----------|
| `gallery` | Car | Atribut multivalue (daftar foto) disimpan sebagai **JSON array** pada satu kolom `gallery` (cast `array`), bukan tabel terpisah. |
| `sim_photo`, `avatar`, `payment_proof`, `delivery_proof` | User / Booking | Hanya menyimpan **path** berkas; file fisik berada di storage (disajikan ber-otorisasi via `SecureFileController`). |
| Koordinat `pickup_lat/lng`, `dropoff_lat/lng` | Booking | Tetap sebagai atribut `decimal(10,7)` pada record bookings. |
| `midtrans_response` | Booking | Respons mentah Midtrans disimpan sebagai **JSON** pada satu kolom (audit trail), bukan tabel terpisah. |

## Langkah Transformasi (Ringkas)

1. **Setiap entitas menjadi satu record** → `users`, `cars`, `drivers`, `bookings`.
2. **Setiap atribut entitas menjadi field** pada record yang bersesuaian.
3. **Tentukan primary key** tiap record (semua memakai surrogate key `id` bigint auto-increment).
4. **Petakan relasi** sesuai tabel di atas — letakkan foreign key pada sisi yang benar.
5. **Tarik garis penghubung** dari setiap foreign key ke primary key record yang direferensikan.
6. **Terapkan aturan integritas** (`ON DELETE restrict` / `cascade` / `set null`) sesuai definisi migration.

Hasil akhir transformasi divisualisasikan pada dokumen
[09-logical-record-structure.md](09-logical-record-structure.md) dan dirinci pada
[10-spesifikasi-database.md](10-spesifikasi-database.md).
