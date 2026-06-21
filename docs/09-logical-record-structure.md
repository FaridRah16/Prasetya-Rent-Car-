# Logical Record Structure (LRS)

LRS adalah hasil transformasi ERD ([08-transformasi-erd-lrs.md](08-transformasi-erd-lrs.md))
yang menampilkan setiap entitas sebagai **record** (kotak tabel) lengkap dengan field,
**primary key (PK)**, **foreign key (FK)**, serta garis penghubung antar record. Terdiri
dari **5 record**, tanpa tabel penghubung (tidak ada relasi M:N).

> Notasi: **PK** = primary key, **FK** = foreign key. Garis menghubungkan FK pada satu
> record ke PK record yang direferensikan, diberi keterangan aturan `ON DELETE`.

```mermaid
erDiagram
    users ||--o| drivers  : "user_id (cascade)"
    users ||--o{ bookings : "user_id (cascade)"
    users ||--o{ bookings : "driver_id (set null)"
    cars  ||--o{ bookings : "car_id (cascade)"
    users ||--o{ reviews  : "user_id (cascade)"
    bookings ||--o| reviews : "booking_id (cascade)"

    users {
        bigint id PK
        varchar name
        varchar email UK
        timestamp email_verified_at
        varchar password
        enum role
        enum verification_status
        varchar phone
        varchar whatsapp_number
        varchar avatar
        varchar sim_photo
        timestamp verified_at
        varchar remember_token
        timestamp created_at
        timestamp updated_at
    }

    cars {
        bigint id PK
        varchar name
        varchar brand
        varchar type
        year year
        varchar color
        varchar plate_number UK
        decimal price_per_day
        enum status
        varchar image
        text gallery
        int seats
        text description
        timestamp created_at
        timestamp updated_at
    }

    drivers {
        bigint id PK
        bigint user_id FK
        varchar license_number UK
        enum status
        timestamp created_at
        timestamp updated_at
    }

    bookings {
        bigint id PK
        bigint user_id FK
        bigint car_id FK
        bigint driver_id FK
        date start_date
        time pickup_time
        date end_date
        time return_time
        int total_days
        decimal total_price
        varchar pickup_location
        decimal pickup_lat
        decimal pickup_lng
        varchar dropoff_location
        decimal dropoff_lat
        decimal dropoff_lng
        enum status
        enum payment_status
        varchar payment_proof
        varchar delivery_proof
        text notes
        timestamp created_at
        timestamp updated_at
    }

    reviews {
        bigint id PK
        bigint booking_id FK
        bigint user_id FK
        int rating
        text comment
        timestamp created_at
        timestamp updated_at
    }
```

## Ringkasan Record & Kunci

| Record | Primary Key | Foreign Key | Merefensikan |
|--------|-------------|-------------|--------------|
| **users** | `id` | — | — |
| **cars** | `id` | — | — |
| **drivers** | `id` | `user_id` | `users.id` |
| **bookings** | `id` | `user_id`, `car_id`, `driver_id` | `users.id`, `cars.id`, `users.id` |
| **reviews** | `id` | `booking_id`, `user_id` | `bookings.id`, `users.id` |

## Aturan Integritas Referensial

| Foreign Key | ON DELETE | Efek |
|-------------|-----------|------|
| `drivers.user_id` → `users.id` | CASCADE | Hapus user → profil driver ikut terhapus |
| `bookings.user_id` → `users.id` | CASCADE | Hapus user → booking miliknya terhapus |
| `bookings.driver_id` → `users.id` | SET NULL | Hapus user-driver → `driver_id` booking jadi NULL |
| `bookings.car_id` → `cars.id` | CASCADE | Hapus mobil → booking mobil tsb terhapus |
| `reviews.booking_id` → `bookings.id` | CASCADE | Hapus booking → review terkait terhapus |
| `reviews.user_id` → `users.id` | CASCADE | Hapus user → review miliknya terhapus |

Spesifikasi field lengkap (tipe, panjang, null, default) terdapat pada
[10-spesifikasi-database.md](10-spesifikasi-database.md).
