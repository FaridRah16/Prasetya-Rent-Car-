# Class Diagram

Class diagram menampilkan struktur kelas aplikasi: **Model Eloquent** (beserta atribut,
relasi, dan method bisnis) serta **Controller**. Atribut dan method diambil 100% sesuai
kode pada `app/Models` dan `app/Http/Controllers`.

## 1. Model (Domain)

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string email
        +string password
        +string role
        +string phone
        +string whatsapp_number
        +string avatar
        +bookings() HasMany
        +driver() HasOne
        +reviews() HasMany
        +bookingsAsDriver() HasMany
        +isAdmin() bool
        +isCustomer() bool
        +isDriver() bool
    }

    class Car {
        +int id
        +string name
        +string brand
        +string type
        +year year
        +string color
        +string plate_number
        +decimal price_per_day
        +string status
        +string image
        +array gallery
        +int seats
        +string description
        +bookings() HasMany
        +isAvailable() bool
        +isRented() bool
        +isMaintenance() bool
        +scopeAvailable(query)
        +getImageUrlAttribute() string
        +getGalleryUrlsAttribute() array
    }

    class Driver {
        +int id
        +int user_id
        +string license_number
        +string status
        +user() BelongsTo
        +bookings() HasMany
        +isAvailable() bool
        +isOnDuty() bool
        +scopeAvailable(query)
    }

    class Booking {
        +int id
        +int user_id
        +int car_id
        +int driver_id
        +date start_date
        +time pickup_time
        +date end_date
        +time return_time
        +int total_days
        +decimal total_price
        +string pickup_location
        +decimal pickup_lat
        +decimal pickup_lng
        +string dropoff_location
        +decimal dropoff_lat
        +decimal dropoff_lng
        +string status
        +string payment_status
        +string payment_proof
        +string delivery_proof
        +string notes
        +user() BelongsTo
        +car() BelongsTo
        +driver() BelongsTo
        +review() HasOne
        +isPending() bool
        +isConfirmed() bool
        +isOngoing() bool
        +isCompleted() bool
        +isCancelled() bool
        +isPaid() bool
        +isUnpaid() bool
        +scopePending(query)
        +scopeConfirmed(query)
        +scopeOngoing(query)
        +getPaymentProofUrlAttribute() string
    }

    class Review {
        +int id
        +int booking_id
        +int user_id
        +int rating
        +string comment
        +booking() BelongsTo
        +user() BelongsTo
    }

    User "1" --> "0..*" Booking : user_id
    User "1" --> "0..1" Driver : user_id
    User "1" --> "0..*" Review : user_id
    User "1" --> "0..*" Booking : driver_id
    Car "1" --> "0..*" Booking : car_id
    Booking "1" --> "0..1" Review : booking_id
    Driver "*" --> "1" User : belongsTo
```

## 2. Controller

```mermaid
classDiagram
    class Controller {
        <<abstract>>
    }

    class AuthController {
        +showLoginForm()
        +login(Request)
        +showRegisterForm()
        +register(Request)
        +logout(Request)
    }

    class PublicCarController {
        +index(Request)
        +show(id)
    }

    class AdminDashboardController {
        +index()
    }
    class AdminCarController {
        +index(Request)
        +create()
        +store(Request)
        +show(id)
        +edit(id)
        +update(Request, id)
        +destroy(id)
        +toggleStatus(id)
    }
    class AdminBookingController {
        +index(Request)
        +show(id)
        +updateStatus(Request, id)
        +assignDriver(Request, id)
        +verifyPayment(id)
        +rejectPayment(Request, id)
    }
    class AdminUserController {
        +index(Request)
        +create()
        +store(Request)
        +show(id)
        +edit(id)
        +update(Request, id)
        +destroy(id)
    }
    class AdminReportController {
        +index()
    }

    class CustomerDashboardController {
        +index()
    }
    class CustomerBookingController {
        +index()
        +create(Request)
        +store(Request)
        +show(id)
        +uploadPayment(Request, id)
        +cancel(id)
    }
    class CustomerProfileController {
        +edit()
        +update(Request)
        +editPassword()
        +updatePassword(Request)
        +deleteAvatar()
    }

    class DriverDashboardController {
        +index()
    }
    class DriverTaskController {
        +index()
        +history()
        +show(id)
        +startTask(id)
        +completeTask(Request, id)
    }

    Controller <|-- AuthController
    Controller <|-- PublicCarController
    Controller <|-- AdminDashboardController
    Controller <|-- AdminCarController
    Controller <|-- AdminBookingController
    Controller <|-- AdminUserController
    Controller <|-- AdminReportController
    Controller <|-- CustomerDashboardController
    Controller <|-- CustomerBookingController
    Controller <|-- CustomerProfileController
    Controller <|-- DriverDashboardController
    Controller <|-- DriverTaskController
```

## 3. Middleware & Ketergantungan Controller–Model

```mermaid
classDiagram
    class RoleMiddleware {
        +handle(Request, Closure, string role) Response
    }

    class CustomerBookingController
    class AdminBookingController
    class DriverTaskController
    class Booking
    class Car
    class Driver
    class User

    RoleMiddleware ..> User : cek role
    CustomerBookingController ..> Booking : create/update
    CustomerBookingController ..> Car : lock & cek status
    CustomerBookingController ..> Driver : cek ketersediaan
    AdminBookingController ..> Booking : updateStatus/verify
    AdminBookingController ..> Car : ubah status
    AdminBookingController ..> Driver : assign/release
    DriverTaskController ..> Booking : start/complete
    DriverTaskController ..> Car : set rented
    DriverTaskController ..> Driver : set on_duty
```

## Catatan Desain

- Semua model menggunakan atribut PHP `#[Fillable([...])]` (fitur Laravel 13) sebagai
  pengganti properti `$fillable`.
- `User` meng-extend `Authenticatable` dan menggunakan trait `HasFactory`, `Notifiable`.
- `Car.gallery` dan `Booking` tanggal/harga menggunakan **cast** (`array`, `date`, `decimal:2`).
- Otorisasi peran disentralisasi pada `RoleMiddleware` yang didaftarkan sebagai alias `role`.
- Password otomatis di-hash melalui cast `hashed` pada model `User`.
