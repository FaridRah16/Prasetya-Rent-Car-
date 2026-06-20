# Entity Relationship Diagram (ERD)

ERD menggambarkan entitas inti aplikasi Prasetya Rent Car beserta atribut dan relasinya.
Tabel pendukung framework (`sessions`, `password_reset_tokens`, `cache`, `jobs`) tidak
ditampilkan karena bukan bagian dari domain bisnis.

> Catatan penting: kolom `bookings.driver_id` mereferensikan **`users.id`** (bukan
> `drivers.id`), karena penugasan driver dilakukan terhadap akun User berperan `driver`.
> Profil `drivers` terhubung ke `users` melalui `drivers.user_id`.

```mermaid
erDiagram
    USERS ||--o{ BOOKINGS : "membuat (user_id)"
    USERS ||--o| DRIVERS : "memiliki profil (user_id)"
    USERS ||--o{ REVIEWS : "menulis (user_id)"
    USERS ||--o{ BOOKINGS : "ditugaskan sbg driver (driver_id)"
    CARS  ||--o{ BOOKINGS : "disewa pada (car_id)"
    BOOKINGS ||--o| REVIEWS : "menghasilkan (booking_id)"

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
        bigint user_id FK "-> users.id, cascade"
        bigint car_id FK "-> cars.id, cascade"
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
        string payment_proof "nullable"
        string delivery_proof "nullable"
        text notes "nullable"
        timestamp created_at
        timestamp updated_at
    }

    REVIEWS {
        bigint id PK
        bigint booking_id FK "-> bookings.id, cascade"
        bigint user_id FK "-> users.id, cascade"
        int rating "unsigned"
        text comment "nullable"
        timestamp created_at
        timestamp updated_at
    }
```

## Keterangan Relasi

| Relasi | Kardinalitas | Foreign Key | On Delete |
|--------|--------------|-------------|-----------|
| User → Booking (pemesan) | 1 : N | `bookings.user_id` | cascade |
| User → Driver (profil) | 1 : 1 | `drivers.user_id` | cascade |
| User → Review | 1 : N | `reviews.user_id` | cascade |
| User → Booking (sebagai driver) | 1 : N | `bookings.driver_id` | set null |
| Car → Booking | 1 : N | `bookings.car_id` | cascade |
| Booking → Review | 1 : 1 | `reviews.booking_id` | cascade |
