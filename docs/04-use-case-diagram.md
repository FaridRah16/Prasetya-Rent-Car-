# Use Case Diagram

Use case dipetakan langsung dari definisi rute (`routes/web.php`) dan method controller.
Terdapat 4 aktor: **Guest** (belum login), **Customer**, **Admin**, dan **Driver**.
Aktor sistem eksternal **Midtrans** memicu webhook notifikasi pembayaran.

## Diagram Use Case Keseluruhan

```mermaid
flowchart LR
    Guest(("👤 Guest"))
    Customer(("👤 Customer"))
    Admin(("👤 Admin"))
    Driver(("👤 Driver"))
    Midtrans(("🏦 Midtrans"))

    subgraph SYS["Sistem Prasetya Rent Car"]
        UC1["Lihat Beranda & Katalog Mobil"]
        UC2["Lihat Detail Mobil"]
        UC3["Registrasi Akun"]
        UC4["Login"]
        UC5["Logout"]
        UC24["Reset Password (lupa password)"]

        UC6["Buat Booking"]
        UC7["Lihat Daftar/Detail Booking"]
        UC8["Bayar Online (Midtrans Snap)"]
        UC8b["Upload Bukti Transfer Manual"]
        UC8c["Cek Status Pembayaran"]
        UC9["Batalkan Booking"]
        UC10["Kelola Profil & Password"]
        UC22["Verifikasi Akun (telepon + foto SIM)"]

        UC11["Dashboard Admin"]
        UC12["Kelola Mobil (CRUD)"]
        UC13["Kelola User/Driver (CRUD)"]
        UC14["Kelola Booking & Update Status"]
        UC15["Verifikasi / Tolak Pembayaran Manual"]
        UC16["Tugaskan Driver"]
        UC17["Lihat Laporan"]
        UC23["Konfirmasi / Tolak Verifikasi Akun"]

        UC18["Lihat Daftar Tugas"]
        UC19["Mulai Tugas"]
        UC20["Upload Bukti Pengantaran"]
        UC21["Lihat Riwayat Tugas"]

        UC25["Terima Webhook Notifikasi Pembayaran"]
    end

    Guest --- UC1 & UC2 & UC3 & UC4 & UC24

    Customer --- UC4
    Customer --- UC5
    Customer --- UC1
    Customer --- UC2
    Customer --- UC6
    Customer --- UC7
    Customer --- UC8
    Customer --- UC8b
    Customer --- UC8c
    Customer --- UC9
    Customer --- UC10
    Customer --- UC22

    Admin --- UC4
    Admin --- UC5
    Admin --- UC11
    Admin --- UC12
    Admin --- UC13
    Admin --- UC14
    Admin --- UC15
    Admin --- UC16
    Admin --- UC17
    Admin --- UC23

    Driver --- UC4
    Driver --- UC5
    Driver --- UC18
    Driver --- UC19
    Driver --- UC20
    Driver --- UC21

    Midtrans --- UC25
```

## Relasi `<<include>>` & `<<extend>>`

```mermaid
flowchart TD
    UC22["Verifikasi Akun"]
    UC23["Konfirmasi Verifikasi oleh Admin"]
    UC6["Buat Booking"]
    UC6a["Cek Ketersediaan Mobil"]
    UC6b["Hitung Total Harga & Hari"]
    UC6c["Pilih Lokasi via Peta"]
    UC8["Bayar Online (Midtrans Snap)"]
    UC8a["Generate Snap Token"]
    UC8d["Sinkronisasi Status ke Midtrans API"]
    UC25["Webhook Notifikasi"]
    UC25a["Verifikasi Signature Key"]
    UC15["Verifikasi Pembayaran Manual"]
    UC15a["Konfirmasi Booking Otomatis"]
    UC19["Mulai Tugas"]
    UC19a["Set Mobil = rented & Driver = on_duty"]

    UC22 -. include .-> UC23
    UC6 -. "«precondition» akun terverifikasi" .-> UC22
    UC6 -. include .-> UC6a
    UC6 -. include .-> UC6b
    UC6 -. extend .-> UC6c
    UC8 -. include .-> UC8a
    UC8 -. extend .-> UC8d
    UC25 -. include .-> UC25a
    UC25 -. include .-> UC15a
    UC15 -. include .-> UC15a
    UC19 -. include .-> UC19a
```

> **Precondition penting:** Use case **Buat Booking (UC6)** hanya dapat dijalankan jika
> akun customer berstatus `verified`. Jika belum, sistem mengarahkan customer ke
> **Verifikasi Akun (UC22)** terlebih dahulu.

> **Dua jalur pembayaran:** **Bayar Online (UC8)** lewat Midtrans Snap (utama) atau
> **Upload Bukti Transfer Manual (UC8b)** lalu diverifikasi admin (UC15). Pembayaran
> online dikonfirmasi via **Webhook (UC25)** atau **Cek Status manual (UC8c)**.

## Rincian Use Case per Aktor

### Guest (publik, tanpa login)
| Use Case | Rute | Controller |
|----------|------|-----------|
| Lihat beranda | `GET /` | closure (featured cars) |
| Katalog mobil | `GET /cars` | `Public\CarController@index` |
| Detail mobil | `GET /cars/{id}` | `Public\CarController@show` |
| Halaman about/contact | `GET /about`, `/contact` | closure |
| Registrasi | `GET/POST /register` | `Auth\AuthController@register` |
| Login | `GET/POST /login` | `Auth\AuthController@login` |
| Lupa password | `GET/POST /forgot-password` | `Auth\AuthController@sendResetLink` |
| Reset password | `GET/POST /reset-password` | `Auth\AuthController@resetPassword` |

### Customer
| Use Case | Rute | Controller |
|----------|------|-----------|
| Dashboard | `GET /customer/dashboard` | `Customer\DashboardController@index` |
| Daftar booking | `GET /customer/bookings` | `Customer\BookingController@index` |
| Form booking | `GET /customer/bookings/create` | `Customer\BookingController@create` |
| Simpan booking | `POST /customer/bookings` | `Customer\BookingController@store` |
| Detail booking | `GET /customer/bookings/{id}` | `Customer\BookingController@show` |
| Halaman bayar (Snap) | `GET /customer/bookings/{booking}/payment` | `Customer\PaymentController@show` |
| Regenerate Snap token | `POST .../payment/token` | `Customer\PaymentController@regenerateToken` |
| Callback selesai | `GET .../payment/finish` | `Customer\PaymentController@finish` |
| Callback belum selesai | `GET .../payment/unfinish` | `Customer\PaymentController@unfinish` |
| Callback error | `GET .../payment/error` | `Customer\PaymentController@error` |
| Cek status pembayaran | `POST .../payment/check-status` | `Customer\PaymentController@checkStatus` |
| Upload bukti transfer manual | `POST .../upload-payment` | `Customer\BookingController@uploadPayment` |
| Batalkan booking | `POST .../cancel` | `Customer\BookingController@cancel` |
| Edit profil | `GET/PUT /customer/profile` | `Customer\ProfileController@edit/update` |
| Ganti password | `GET/PUT /customer/profile/password` | `Customer\ProfileController@editPassword/updatePassword` |
| Hapus avatar | `DELETE /customer/profile/avatar` | `Customer\ProfileController@deleteAvatar` |
| Ajukan verifikasi akun | `POST /customer/profile/verification` | `Customer\ProfileController@submitVerification` |

### Admin
| Use Case | Rute | Controller |
|----------|------|-----------|
| Dashboard | `GET /admin/dashboard` | `Admin\DashboardController@index` |
| CRUD mobil | `GET/POST/PUT/DELETE /admin/cars...` | `Admin\CarController` |
| Toggle status mobil | `POST /admin/cars/{id}/toggle-status` | `Admin\CarController@toggleStatus` |
| Daftar/detail booking | `GET /admin/bookings...` | `Admin\BookingController@index/show` |
| Update status booking | `POST /admin/bookings/{id}/update-status` | `Admin\BookingController@updateStatus` |
| Tugaskan driver | `POST /admin/bookings/{id}/assign-driver` | `Admin\BookingController@assignDriver` |
| Verifikasi pembayaran | `POST /admin/bookings/{id}/verify-payment` | `Admin\BookingController@verifyPayment` |
| Tolak pembayaran | `POST /admin/bookings/{id}/reject-payment` | `Admin\BookingController@rejectPayment` |
| CRUD user | `GET/POST/PUT/DELETE /admin/users...` | `Admin\UserController` |
| Verifikasi akun user | `POST /admin/users/{id}/verify` | `Admin\UserController@verifyUser` |
| Tolak verifikasi akun | `POST /admin/users/{id}/reject-verification` | `Admin\UserController@rejectVerification` |
| Laporan | `GET /admin/reports` | `Admin\ReportController@index` |

### Driver
| Use Case | Rute | Controller |
|----------|------|-----------|
| Dashboard | `GET /driver/dashboard` | `Driver\DashboardController@index` |
| Daftar tugas | `GET /driver/tasks` | `Driver\TaskController@index` |
| Riwayat tugas | `GET /driver/tasks/history` | `Driver\TaskController@history` |
| Detail tugas | `GET /driver/tasks/{id}` | `Driver\TaskController@show` |
| Mulai tugas | `POST /driver/tasks/{id}/start` | `Driver\TaskController@startTask` |
| Selesai (upload bukti) | `POST /driver/tasks/{id}/complete` | `Driver\TaskController@completeTask` |

### Sistem / Eksternal
| Use Case | Rute | Controller |
|----------|------|-----------|
| Webhook notifikasi Midtrans | `POST /api/payment/notification` | `PaymentNotificationController@handle` |
| Akses berkas PII ber-otorisasi | `GET /secure/users/{id}/sim`, `/secure/bookings/{id}/payment`, `/secure/bookings/{id}/delivery` | `SecureFileController` |
| Auto-batal booking kedaluwarsa | command `bookings:expire-pending` (terjadwal) | `ExpirePendingBookings` |

> Otorisasi ditegakkan oleh `RoleMiddleware` (`role:admin`, `role:customer`, `role:driver`)
> pada masing-masing grup rute. Webhook `/api/payment/notification` **dikecualikan** dari
> CSRF & `auth` (dipanggil server-to-server Midtrans), diamankan dengan **signature key**.
