# Relasi Tabel

Dokumen ini memetakan relasi antar tabel sebagaimana didefinisikan pada migration
(foreign key) dan model Eloquent (relasi `hasMany`, `hasOne`, `belongsTo`).

## Diagram Relasi (Graph)

```mermaid
graph TD
    USERS[("users")]
    CARS[("cars")]
    DRIVERS[("drivers")]
    BOOKINGS[("bookings")]

    USERS -->|"hasOne · drivers.user_id"| DRIVERS
    USERS -->|"hasMany · bookings.user_id"| BOOKINGS
    USERS -->|"hasMany (bookingsAsDriver) · bookings.driver_id"| BOOKINGS
    CARS -->|"hasMany · bookings.car_id"| BOOKINGS

    DRIVERS -.->|"belongsTo · user_id"| USERS
    BOOKINGS -.->|"belongsTo (user) · user_id"| USERS
    BOOKINGS -.->|"belongsTo (car) · car_id"| CARS
    BOOKINGS -.->|"belongsTo (driver) · driver_id"| USERS

    classDef tbl fill:#e8f0fe,stroke:#1a73e8,stroke-width:1px,color:#111;
    class USERS,CARS,DRIVERS,BOOKINGS tbl;
```

> Garis penuh = relasi "memiliki" (`hasMany` / `hasOne`).
> Garis putus-putus = relasi "milik" (`belongsTo`).

## Pemetaan Relasi Eloquent

### Model `User`
| Method | Tipe | Target | Foreign Key |
|--------|------|--------|-------------|
| `bookings()` | hasMany | Booking | `user_id` |
| `driver()` | hasOne | Driver | `user_id` |
| `bookingsAsDriver()` | hasMany | Booking | `driver_id` |

### Model `Car`
| Method | Tipe | Target | Foreign Key |
|--------|------|--------|-------------|
| `bookings()` | hasMany | Booking | `car_id` |
| `activeBooking()` | hasOne | Booking | `car_id` (status `ongoing`, `end_date` terbaru) |

### Model `Driver`
| Method | Tipe | Target | Key |
|--------|------|--------|-----|
| `user()` | belongsTo | User | `user_id` |
| `bookings()` | hasMany | Booking | `driver_id` → `user_id` |

### Model `Booking`
| Method | Tipe | Target | Foreign Key |
|--------|------|--------|-------------|
| `user()` | belongsTo | User | `user_id` |
| `car()` | belongsTo | Car | `car_id` |
| `driver()` | belongsTo | User | `driver_id` |

> Catatan: model `Review` dan relasinya (`User::reviews()`, `Booking::review()`) sudah
> **dihapus** seiring penghapusan fitur review.

## Aturan Integritas Referensial (On Delete)

```mermaid
graph LR
    A[Hapus User] -->|cascade| B[Hapus Driver miliknya]
    A -->|restrict| C[Ditolak jika masih punya Booking]
    A -->|set null| E[driver_id booking jadi NULL]
    F[Hapus Car] -->|restrict| G[Ditolak jika masih punya Booking]
```

> FK `bookings.user_id` & `bookings.car_id` menggunakan **RESTRICT**: user/mobil yang
> masih memiliki riwayat booking tidak dapat dihapus, melindungi data finansial & audit.
> `drivers.user_id` tetap **CASCADE**; `bookings.driver_id` tetap **SET NULL**.
