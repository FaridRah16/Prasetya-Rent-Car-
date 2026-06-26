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

## 2. Verifikasi Akun (Customer Ajukan, Admin Konfirmasi)

```mermaid
sequenceDiagram
    actor C as Customer
    actor A as Admin
    participant PC as Customer\ProfileController
    participant UC as Admin\UserController
    participant S as Storage (public)
    participant U as User Model

    C->>PC: POST /customer/profile/verification (phone, sim_photo)
    alt status verified / pending
        PC-->>C: error 'sudah terverifikasi / menunggu konfirmasi'
    else status unverified
        PC->>PC: validate(phone angka, sim image max 2MB)
        opt ada sim_photo lama
            PC->>S: delete(sim_photo lama)
        end
        PC->>S: store('sim_photos')
        S-->>PC: path
        PC->>U: update(phone, sim_photo, verification_status=pending)
        PC-->>C: 'Pengajuan dikirim, menunggu konfirmasi admin'
    end

    A->>UC: POST /admin/users/{id}/verify
    UC->>U: findOrFail(id)
    alt sim_photo kosong
        UC-->>A: error 'user belum unggah SIM'
    else ada sim_photo
        UC->>U: update(verification_status=verified, verified_at=now)
        UC-->>A: 'Akun berhasil diverifikasi'
    end

    opt admin menolak
        A->>UC: POST /admin/users/{id}/reject-verification
        UC->>S: delete(sim_photo)
        UC->>U: update(verification_status=unverified, sim_photo=null, verified_at=null)
        UC-->>A: 'Verifikasi ditolak'
    end
```

## 3. Membuat Booking (dengan proteksi race condition)

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
    BC->>BC: cek Auth::user()->isVerified()
    alt belum verified
        BC-->>C: redirect ke profil 'selesaikan verifikasi dahulu'
    end
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

## 4. Upload Bukti Bayar Manual & Verifikasi Admin

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

## 4b. Pembayaran Online via Midtrans Snap

```mermaid
sequenceDiagram
    actor C as Customer
    participant PC as Customer\PaymentController
    participant MS as MidtransService
    participant MT as Midtrans (Snap API)
    participant Bk as Booking Model

    C->>PC: GET /customer/bookings/{id}/payment
    PC->>Bk: where user_id ->findOrFail(id)
    alt status != pending / sudah paid
        PC-->>C: redirect ke detail booking
    else isPaymentExpired()
        PC->>Bk: update(status=cancelled)
        PC-->>C: 'Batas waktu pembayaran habis, dibatalkan'
    else masih pending & valid
        PC->>MS: createSnapToken(booking)
        MS->>MS: configure() + set order_id baru (unik)
        MS->>MT: Snap::getSnapToken(params + expiry)
        MT-->>MS: snap_token
        MS->>Bk: update(order_id, snap_token, gross_amount)
        MS-->>PC: snap_token
        PC-->>C: tampilkan halaman Snap (redirect vtweb)
    end

    C->>MT: Pilih metode & bayar (VA/GoPay/QRIS)
    MT-->>C: redirect callback finish/unfinish/error
    C->>PC: GET .../payment/finish
    PC->>MS: syncStatusFromMidtrans(booking)
    MS->>MT: Transaction::status(order_id)
    MT-->>MS: transaction_status
    MS->>MS: handleNotification(status)
    MS->>Bk: update(payment_status, status, payment_type, dll)
    PC->>Bk: refresh()
    alt paid & confirmed
        PC-->>C: '✅ Pembayaran berhasil, booking dikonfirmasi'
    else masih pending
        PC-->>C: '⏳ Masih diproses, klik Cek Status'
    end
```

## 4c. Webhook Notifikasi Midtrans (Server-to-Server)

```mermaid
sequenceDiagram
    participant MT as Midtrans
    participant R as Route /api/payment/notification
    participant NC as PaymentNotificationController
    participant MS as MidtransService
    participant Bk as Booking Model

    MT->>R: POST /api/payment/notification (tanpa CSRF/auth)
    R->>NC: handle(Request)
    NC->>NC: isValidSignature() — SHA512(order_id+status_code+gross_amount+server_key)
    alt signature tidak valid
        NC-->>MT: 400 Invalid signature
    else valid
        NC->>MS: handleNotification(request->all())
        MS->>Bk: where order_id ->first()
        alt booking tidak ditemukan
            MS-->>NC: success=false
            NC-->>MT: 404 Booking not found
        else ditemukan
            MS->>Bk: simpan midtrans_response (audit)
            MS->>MS: map transaction_status -> status internal
            MS->>Bk: update(payment_status, status, payment_type, dll)
            MS-->>NC: success=true
            NC-->>MT: 200 OK
        end
    end
```

> Mapping status Midtrans → internal: `settlement`/`capture(accept)` → `paid`+`confirmed`;
> `pending` → tetap `pending`; `expire`/`cancel`/`refund` → `cancelled`; `deny` → tetap `pending`.

## 5. Driver Mulai Tugas & Upload Bukti Pengantaran

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

## 6. Admin Tugaskan Driver

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

## 7. Customer Batalkan Booking

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
