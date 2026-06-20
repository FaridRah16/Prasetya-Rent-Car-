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
    REVIEWS[("reviews")]

    USERS -->|"hasOne · drivers.user_id"| DRIVERS
    USERS -->|"hasMany · bookings.user_id"| BOOKINGS
    USERS -->|"hasMany (bookingsAsDriver) · bookings.driver_id"| BOOKINGS
    USERS -->|"hasMany · reviews.user_id"| REVIEWS
    CARS -->|"hasMany · bookings.car_id"| BOOKINGS
    BOOKINGS -->|"hasOne · reviews.booking_id"| REVIEWS

    DRIVERS -.->|"belongsTo · user_id"| USERS
    BOOKINGS -.->|"belongsTo (user) · user_id"| USERS
    BOOKINGS -.->|"belongsTo (car) · car_id"| CARS
    BOOKINGS -.->|"belongsTo (driver) · driver_id"| USERS
    REVIEWS -.->|"belongsTo · booking_id / user_id"| BOOKINGS

    classDef tbl fill:#e8f0fe,stroke:#1a73e8,stroke-width:1px,color:#111;
    class USERS,CARS,DRIVERS,BOOKINGS,REVIEWS tbl;
```

> Garis penuh = relasi "memiliki" (`hasMany` / `hasOne`).
> Garis putus-putus = relasi "milik" (`belongsTo`).

## Pemetaan Relasi Eloquent

### Model `User`
| Method | Tipe | Target | Foreign Key |
|--------|------|--------|-------------|
| `bookings()` | hasMany | Booking | `user_id` |
| `driver()` | hasOne | Driver | `user_id` |
| `reviews()` | hasMany | Review | `user_id` |
| `bookingsAsDriver()` | hasMany | Booking | `driver_id` |

### Model `Car`
| Method | Tipe | Target | Foreign Key |
|--------|------|--------|-------------|
| `bookings()` | hasMany | Booking | `car_id` |

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
| `review()` | hasOne | Review | `booking_id` |

### Model `Review`
| Method | Tipe | Target | Foreign Key |
|--------|------|--------|-------------|
| `booking()` | belongsTo | Booking | `booking_id` |
| `user()` | belongsTo | User | `user_id` |

## Aturan Integritas Referensial (On Delete)

```mermaid
graph LR
    A[Hapus User] -->|cascade| B[Hapus Driver miliknya]
    A -->|cascade| C[Hapus Booking miliknya]
    A -->|cascade| D[Hapus Review miliknya]
    A -->|set null| E[driver_id booking jadi NULL]
    F[Hapus Car] -->|cascade| G[Hapus Booking mobil tsb]
    H[Hapus Booking] -->|cascade| I[Hapus Review terkait]
```
