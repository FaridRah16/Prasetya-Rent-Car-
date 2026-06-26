# Class Diagram

Class diagram menampilkan struktur kelas aplikasi: **Model Eloquent** (beserta atribut,
relasi, dan method bisnis), **Controller**, dan **Service**. Atribut dan method diambil
100% sesuai kode pada `app/Models`, `app/Http/Controllers`, dan `app/Services`.

## 1. Model (Domain)

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string email
        +string password
        +string role
        +string verification_status
        +string phone
        +string whatsapp_number
        +string avatar
        +string sim_photo
        +datetime verified_at
        +bookings() HasMany
        +driver() HasOne
        +bookingsAsDriver() HasMany
        +isAdmin() bool
        +isCustomer() bool
        +isDriver() bool
        +isVerified() bool
        +isPendingVerification() bool
        +isUnverified() bool
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
        +string transmission
        +string fuel
        +string description
        +bookings() HasMany
        +activeBooking() HasOne
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
        +string order_id
        +string snap_token
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
        +string payment_type
        +string payment_channel
        +string transaction_status
        +datetime transaction_time
        +datetime settlement_time
        +decimal gross_amount
        +json midtrans_response
        +string notes
        +user() BelongsTo
        +car() BelongsTo
        +driver() BelongsTo
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
        +scopeBlockingSlot(query)
        +paymentDeadline() Carbon
        +isPaymentExpired() bool
        +getPaymentProofUrlAttribute() string
    }

    User "1" --> "0..*" Booking : user_id
    User "1" --> "0..1" Driver : user_id
    User "1" --> "0..*" Booking : driver_id
    Car "1" --> "0..*" Booking : car_id
    Driver "*" --> "1" User : belongsTo
```

> Fitur **Review** sudah dihapus — class `Review` beserta relasinya tidak lagi ada.

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
        +showForgotPasswordForm()
        +sendResetLink(Request)
        +showResetForm(token)
        +resetPassword(Request)
    }

    class PublicCarController {
        +index(Request)
        +show(id)
    }

    class SecureFileController {
        +sim(id) StreamedResponse
        +payment(id) StreamedResponse
        +delivery(id) StreamedResponse
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
        +verifyUser(id)
        +rejectVerification(id)
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
    class CustomerPaymentController {
        +show(bookingId)
        +regenerateToken(bookingId)
        +finish(Request, bookingId)
        +unfinish(Request, bookingId)
        +error(Request, bookingId)
        +checkStatus(bookingId)
    }
    class CustomerProfileController {
        +edit()
        +update(Request)
        +editPassword()
        +updatePassword(Request)
        +deleteAvatar()
        +submitVerification(Request)
    }

    class PaymentNotificationController {
        +handle(Request)
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
    Controller <|-- SecureFileController
    Controller <|-- AdminDashboardController
    Controller <|-- AdminCarController
    Controller <|-- AdminBookingController
    Controller <|-- AdminUserController
    Controller <|-- AdminReportController
    Controller <|-- CustomerDashboardController
    Controller <|-- CustomerBookingController
    Controller <|-- CustomerPaymentController
    Controller <|-- CustomerProfileController
    Controller <|-- PaymentNotificationController
    Controller <|-- DriverDashboardController
    Controller <|-- DriverTaskController
```

## 3. Service, Command & Ketergantungan

```mermaid
classDiagram
    class MidtransService {
        <<service>>
        +configure()$ void
        +createSnapToken(Booking)$ string
        +handleNotification(notification)$ array
        +verifySignature(orderId, statusCode, grossAmount, serverKey)$ string
    }

    class ExpirePendingBookings {
        <<command>>
        +signature = "bookings:expire-pending"
        +handle() int
    }

    class RoleMiddleware {
        +handle(Request, Closure, string role) Response
    }

    class CustomerPaymentController
    class PaymentNotificationController
    class CustomerBookingController
    class AdminBookingController
    class Booking

    CustomerPaymentController ..> MidtransService : createSnapToken / status
    PaymentNotificationController ..> MidtransService : handleNotification / verifySignature
    MidtransService ..> Booking : update status & data Midtrans
    ExpirePendingBookings ..> Booking : batalkan pending kedaluwarsa
    RoleMiddleware ..> User : cek role
    CustomerBookingController ..> User : cek isVerified() sebelum booking
    CustomerBookingController ..> Booking : create (scopeBlockingSlot)
    AdminBookingController ..> Booking : updateStatus / verifyPayment
```

## Catatan Desain

- Aplikasi berjalan di atas **Laravel 12** (PHP 8.3).
- Model menggunakan atribut PHP `#[Fillable([...])]` / properti `$fillable` untuk mass-assignment.
  Pada `User`, field `role`, `verification_status`, dan `verified_at` **sengaja tidak**
  mass-assignable (cegah privilege-escalation / self-verify).
- `Car.gallery` (cast `array`), `Booking` tanggal/harga (`date`, `decimal:2`), dan
  `Booking.midtrans_response` (JSON) menggunakan **cast**.
- Otorisasi peran disentralisasi pada `RoleMiddleware` (alias `role`). Berkas PII
  (SIM, bukti bayar/antar) disajikan ber-otorisasi lewat `SecureFileController` dari disk privat.
- **Verifikasi akun**: `User.verification_status` `unverified` → `pending` → `verified`.
  Customer **wajib** `verified` (`isVerified()`) sebelum dapat membuat booking.
- **Pembayaran**: `MidtransService` membuat Snap token & memproses notifikasi/sinkronisasi.
  `Booking.paymentDeadline()` / `isPaymentExpired()` menghitung batas waktu bayar;
  `scopeBlockingSlot()` menentukan booking yang masih mengunci slot mobil/driver.
- **Auto-expire**: command `bookings:expire-pending` (dijadwalkan tiap menit pada
  `routes/console.php`) membatalkan booking pending yang melewati batas waktu pembayaran.
