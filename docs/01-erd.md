# Entity Relationship Diagram (ERD)

ERD menggambarkan entitas inti aplikasi Prasetya Rent Car beserta atribut dan relasinya.
Tabel pendukung framework (`sessions`, `password_reset_tokens`, `cache`, `jobs`) tidak
ditampilkan karena bukan bagian dari domain bisnis.

> Catatan penting: kolom `bookings.driver_id` mereferensikan **`users.id`** (bukan
> `drivers.id`), karena penugasan driver dilakukan terhadap akun User berperan `driver`.
> Profil `drivers` terhubung ke `users` melalui `drivers.user_id`.

> Domain terdiri dari **4 entitas** (User, Car, Driver, Booking). Entitas `Review` sudah
> dihapus (tabel `reviews` di-drop lewat migration).

```mermaid
erDiagram
    USERS ||--o{ BOOKINGS : "membuat (user_id)"
    USERS ||--o| DRIVERS : "memiliki profil (user_id)"
    USERS ||--o{ BOOKINGS : "ditugaskan sbg driver (driver_id)"
    CARS  ||--o{ BOOKINGS : "disewa pada (car_id)"

    USERS {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at "nullable"
        string password
        enum role "admin|customer|driver — default customer"
        enum verification_status "unverified|pending|verified — default unverified"
        string phone "nullable"
        string whatsapp_number "nullable, len 20"
        string avatar "nullable"
        string sim_photo "nullable — foto SIM utk verifikasi"
        timestamp verified_at "nullable"
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    CARS {
        bigint id PK
        string name
        string brand
        string type
        year year
        string color
        string plate_number UK
        decimal price_per_day "10,2"
        enum status "available|rented|maintenance — default available"
        string image "nullable"
        text gallery "nullable, cast array"
        int seats
        enum transmission "Manual|Automatic|CVT — nullable"
        enum fuel "Bensin|Diesel|Hybrid|Listrik — nullable"
        text description "nullable"
        timestamp created_at
        timestamp updated_at
    }

    DRIVERS {
        bigint id PK
        bigint user_id FK "-> users.id, cascade"
        string license_number UK
        enum status "available|on_duty — default available"
        timestamp created_at
        timestamp updated_at
    }

    BOOKINGS {
        bigint id PK
        string order_id UK "nullable — order id Midtrans"
        string snap_token "nullable — token Snap Midtrans"
        bigint user_id FK "-> users.id, restrict"
        bigint car_id FK "-> cars.id, restrict"
        bigint driver_id FK "-> users.id, nullable, set null"
        date start_date
        time pickup_time "nullable"
        date end_date
        time return_time "nullable"
        int total_days
        decimal total_price "10,2"
        string pickup_location
        decimal pickup_lat "10,7 nullable"
        decimal pickup_lng "10,7 nullable"
        string dropoff_location
        decimal dropoff_lat "10,7 nullable"
        decimal dropoff_lng "10,7 nullable"
        enum status "pending|confirmed|ongoing|completed|cancelled"
        enum payment_status "unpaid|paid — default unpaid"
        string payment_proof "nullable — bukti transfer manual"
        string delivery_proof "nullable — bukti pengantaran driver"
        string payment_type "nullable — bank_transfer|gopay|qris|dll"
        string payment_channel "nullable — bca|bni|gopay|dll"
        string transaction_status "nullable — status dari Midtrans"
        timestamp transaction_time "nullable"
        timestamp settlement_time "nullable"
        decimal gross_amount "10,2 nullable"
        json midtrans_response "nullable — raw callback (audit)"
        text notes "nullable"
        timestamp created_at
        timestamp updated_at
    }
```

## Keterangan Relasi

| Relasi | Kardinalitas | Foreign Key | On Delete |
|--------|--------------|-------------|-----------|
| User → Booking (pemesan) | 1 : N | `bookings.user_id` | **restrict** |
| User → Driver (profil) | 1 : 1 | `drivers.user_id` | cascade |
| User → Booking (sebagai driver) | 1 : N | `bookings.driver_id` | set null |
| Car → Booking | 1 : N | `bookings.car_id` | **restrict** |

> **Perubahan integritas:** FK `bookings.user_id` dan `bookings.car_id` diubah dari
> `cascade` menjadi **`restrict`** (migration `2026_06_22_120000`). Tujuannya melindungi
> jejak finansial/audit — user atau mobil yang masih punya riwayat booking tidak bisa
> dihapus, sehingga data historis tidak ikut terhapus. `driver_id` tetap `set null`.

## Catatan Kolom Pembayaran (Midtrans)

Kolom `order_id … midtrans_response` ditambahkan lewat migration
`2026_06_23_000000_add_midtrans_fields_to_bookings_table` untuk integrasi **Midtrans Snap**:

- `order_id` — unik, format `BOOKING-{id}-{timestamp}-{random}`, dibuat ulang tiap kali
  Snap token diminta (Midtrans tidak mengizinkan order_id ganda).
- `snap_token` — token untuk membuka halaman pembayaran Snap.
- `transaction_status` — status mentah dari Midtrans (`settlement`, `pending`, `expire`, dll).
- `midtrans_response` — JSON respons callback/notifikasi mentah untuk audit trail.
