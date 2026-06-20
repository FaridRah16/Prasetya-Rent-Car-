# Sequence Diagram

Sequence diagram menggambarkan interaksi antar objek (Aktor → Route/Middleware →
Controller → Model → Database → View) untuk skenario utama. Alur mengikuti implementasi
controller secara akurat.

## 1. Login

```mermaid
sequenceDiagram
    actor U as User
    participant B as Browser
    participant R as Route (web.php)
    participant MW as guest middleware
    participant AC as AuthController
    participant A as Auth (Facade)
    participant DB as Database

    U->>B: Isi email & password
    B->>R: POST /login
    R->>MW: cek belum login
    MW->>AC: login(Request)
    AC->>AC: validate(email, password)
    AC->>A: Auth::attempt(credentials, remember)
    A->>DB: SELECT user WHERE email
    DB-->>A: data user
    alt Kredensial valid
        A-->>AC: true
        AC->>B: session()->regenerate()
        AC->>A: Auth::user() -> cek role
        alt role admin
            AC-->>B: redirect admin.dashboard
        else role driver
            AC-->>B: redirect driver.dashboard
        else role customer
            AC-->>B: redirect customer.dashboard
        end
    else Kredensial salah
        A-->>AC: false
        AC-->>B: ValidationException 'Email atau password salah'
    end
```

## 2. Membuat Booking (dengan proteksi race condition)

```mermaid
sequenceDiagram
    actor C as Customer
    participant B as Browser
    participant MW as auth + role:customer
    participant BC as Customer\BookingController
    participant DB as DB Facade (Transaction)
    participant Car as Car Model
    participant Bk as Booking Model

    C->>B: Isi form booking & submit
    B->>MW: POST /customer/bookings
    MW->>BC: store(Request)
    BC->>BC: validate(input)
    BC->>BC: hitung pickup/return datetime
    BC->>BC: cek return > pickup
    BC->>BC: total_days = ceil(jam/24)
    BC->>DB: DB::transaction(...)
    activate DB
    DB->>Car: lockForUpdate()->findOrFail(car_id)
    Car-->>DB: data mobil (terkunci)
    DB->>DB: cek status == available
    DB->>Bk: query overlap (car_id, tanggal+jam)
    Bk-->>DB: ada/tidak overlap
    opt driver dipilih
        DB->>Bk: query overlap driver_id
        Bk-->>DB: ada/tidak overlap
    end
    DB->>Bk: Booking::create(status=pending, unpaid)
    Bk-->>DB: booking baru
    DB-->>BC: commit
    deactivate DB
    BC-->>B: redirect ke detail booking + pesan sukses
```

## 3. Upload Bukti Bayar & Verifikasi Admin

```mermaid
sequenceDiagram
    actor C as Customer
    actor A as Admin
    participant CBC as Customer\BookingController
    participant ABC as Admin\BookingController
    participant S as Storage (public)
    participant Bk as Booking Model

    C->>CBC: POST upload-payment (file)
    CBC->>CBC: validate(image, max 2MB)
    CBC->>Bk: findOrFail(id) milik user
    opt ada bukti lama
        CBC->>S: delete(payment_proof lama)
    end
    CBC->>S: storeAs('payments', ...)
    S-->>CBC: path
    CBC->>Bk: update(payment_proof = path)
    CBC-->>C: 'Menunggu verifikasi admin'

    A->>ABC: POST verify-payment (id)
    ABC->>Bk: findOrFail(id)
    alt payment_proof kosong
        ABC-->>A: error 'Belum ada bukti'
    else ada bukti
        ABC->>Bk: update(payment_status=paid, status=confirmed)
        ABC-->>A: 'Pembayaran diverifikasi & booking dikonfirmasi'
    end
```

## 4. Driver Mulai Tugas & Upload Bukti Pengantaran

```mermaid
sequenceDiagram
    actor D as Driver
    participant MW as auth + role:driver
    participant TC as Driver\TaskController
    participant Bk as Booking Model
    participant Cr as Car Model
    participant Dr as Driver Model
    participant S as Storage (public)

    D->>MW: POST /driver/tasks/{id}/start
    MW->>TC: startTask(id)
    TC->>Bk: where driver_id=Auth::id, status=confirmed ->findOrFail
    Bk-->>TC: task
    TC->>Bk: update(status=ongoing)
    TC->>Cr: car->update(status=rented)
    TC->>Dr: driver->update(status=on_duty)
    TC-->>D: 'Tugas dimulai'

    D->>TC: POST /driver/tasks/{id}/complete (foto)
    TC->>TC: validate(image, max 5MB)
    TC->>Bk: where driver_id=Auth::id, status=ongoing ->findOrFail
    TC->>S: store('delivery_proofs')
    S-->>TC: path
    opt ada bukti lama
        TC->>S: delete(delivery_proof lama)
    end
    TC->>Bk: update(delivery_proof=path) [status tetap ongoing]
    TC-->>D: 'Menunggu konfirmasi admin'
```

## 5. Admin Tugaskan Driver

```mermaid
sequenceDiagram
    actor A as Admin
    participant ABC as Admin\BookingController
    participant Bk as Booking Model
    participant Dr as Driver Model

    A->>ABC: POST assign-driver (driver_id)
    ABC->>ABC: validate(exists drivers.user_id)
    ABC->>Bk: findOrFail(id)
    alt status completed/cancelled
        ABC-->>A: error 'tidak dapat ditugaskan'
    else
        opt sudah ada driver lama
            ABC->>Dr: oldDriver->update(status=available)
        end
        ABC->>Bk: update(driver_id = baru)
        ABC-->>A: 'Driver berhasil ditugaskan'
    end
```

## 6. Customer Batalkan Booking

```mermaid
sequenceDiagram
    actor C as Customer
    participant CBC as Customer\BookingController
    participant Bk as Booking Model

    C->>CBC: POST /customer/bookings/{id}/cancel
    CBC->>Bk: where user_id=Auth::id ->findOrFail(id)
    alt status != pending
        CBC-->>C: error 'Hanya pending yang bisa dibatalkan'
    else status == pending
        CBC->>Bk: update(status=cancelled)
        CBC-->>C: redirect index 'Booking dibatalkan'
    end
```
