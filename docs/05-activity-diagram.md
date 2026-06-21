# Activity Diagram

Activity diagram menggambarkan alur kerja proses bisnis utama. Setiap diagram mengikuti
logika nyata pada controller terkait.

## 1. Registrasi & Login (Autentikasi)

```mermaid
flowchart TD
    A([Mulai]) --> B[Buka halaman Login/Register]
    B --> C{Sudah punya akun?}
    C -->|Belum| D[Isi form registrasi:<br/>nama, email, phone, password]
    D --> E{Validasi data?}
    E -->|Gagal| D
    E -->|Lolos| F[Buat User role=customer<br/>password di-hash]
    F --> G[Auto login]
    G --> H[Redirect ke customer dashboard]

    C -->|Sudah| I[Isi email & password]
    I --> J{Auth::attempt valid?}
    J -->|Tidak| K[Tampilkan error<br/>'Email atau password salah']
    K --> I
    J -->|Ya| L[Regenerate session]
    L --> M{Cek role}
    M -->|admin| N[Redirect admin dashboard]
    M -->|driver| O[Redirect driver dashboard]
    M -->|customer| H
    H --> Z([Selesai])
    N --> Z
    O --> Z
```

## 2. Verifikasi Akun (Customer & Admin)

```mermaid
flowchart TD
    subgraph Customer
        A([Mulai]) --> B[Buka halaman Profil]
        B --> C{verification_status?}
        C -->|verified| D[Sudah terverifikasi<br/>tidak perlu aksi]
        C -->|pending| E[Menunggu konfirmasi admin]
        C -->|unverified| F[Isi nomor telepon<br/>+ upload foto SIM]
        F --> G{Validasi:<br/>phone angka, SIM image max 2MB?}
        G -->|Gagal| F
        G -->|Lolos| H[Hapus foto SIM lama jika ada]
        H --> I[Simpan sim_photo ke storage/sim_photos<br/>set verification_status=pending]
        I --> E
    end
    subgraph Admin
        E --> J[Buka detail user]
        J --> K{Keputusan admin}
        K -->|Verifikasi| L{Ada sim_photo?}
        L -->|Tidak| M[Error: user belum unggah SIM]
        L -->|Ya| N[verification_status=verified<br/>set verified_at=now]
        N --> O[Akun dapat memesan]
        K -->|Tolak| P[Hapus sim_photo<br/>verification_status=unverified<br/>verified_at=null]
        P --> Q[Customer ajukan ulang]
        Q --> F
    end
    D --> Z([Selesai])
    O --> Z
    M --> J
```

## 3. Membuat Booking (Customer)

```mermaid
flowchart TD
    A([Mulai]) --> A1{Akun verified?}
    A1 -->|Tidak| A2[Redirect ke Profil:<br/>selesaikan verifikasi dahulu]
    A2 --> Z2([Selesai])
    A1 -->|Ya| B[Buka form Buat Booking]
    B --> C[Pilih mobil, tanggal, jam,<br/>lokasi jemput/antar, driver opsional]
    C --> C2[Pilih lokasi via peta - opsional<br/>set lat/lng]
    C2 --> D[Submit]
    D --> E{Validasi input?}
    E -->|Gagal| C
    E -->|Lolos| F[Hitung pickup & return datetime]
    F --> G{return > pickup?}
    G -->|Tidak| H[Error: waktu pengembalian<br/>harus setelah penjemputan]
    H --> C
    G -->|Ya| I[Hitung total_days = ceil jam/24<br/>min 1 hari]
    I --> J[[DB Transaction + lockForUpdate]]
    J --> K[Lock baris mobil]
    K --> L{Mobil status available?}
    L -->|Tidak| M[Throw: Mobil tidak tersedia]
    L -->|Ya| N{Ada booking overlap<br/>tanggal+jam?}
    N -->|Ya| O[Throw: Mobil sudah di-booking]
    N -->|Tidak| P{driver dipilih &<br/>driver overlap?}
    P -->|Ya| Q[Throw: Driver sudah ditugaskan]
    P -->|Tidak| R[Hitung total_price =<br/>total_days x price_per_day]
    R --> S[Create Booking<br/>status=pending, payment_status=unpaid]
    S --> T[Commit transaction]
    T --> U[Redirect ke detail booking<br/>+ instruksi pembayaran]
    U --> Z([Selesai])
    M --> Z
    O --> Z
    Q --> Z
```

## 4. Pembayaran & Verifikasi

```mermaid
flowchart TD
    subgraph Customer
        A([Mulai]) --> B[Buka detail booking pending]
        B --> C[Upload bukti pembayaran<br/>jpeg/png/jpg max 2MB]
        C --> D{Validasi file?}
        D -->|Gagal| C
        D -->|Lolos| E[Hapus bukti lama jika ada]
        E --> F[Simpan file ke storage/payments]
        F --> G[Update payment_proof]
        G --> H[Status: menunggu verifikasi admin]
    end
    subgraph Admin
        H --> I[Buka detail booking]
        I --> J{Keputusan admin}
        J -->|Verifikasi| K{Ada payment_proof?}
        K -->|Tidak| L[Error: belum ada bukti]
        K -->|Ya| M[payment_status=paid<br/>status=confirmed]
        M --> N[Booking terkonfirmasi]
        J -->|Tolak| O[Hapus file bukti<br/>payment_proof=null<br/>payment_status=unpaid]
        O --> P[Customer harus upload ulang]
        P --> C
    end
    N --> Z([Selesai])
    L --> I
```

## 5. Pelaksanaan Tugas (Driver)

```mermaid
flowchart TD
    A([Mulai]) --> B[Driver buka Daftar Tugas<br/>status confirmed/ongoing]
    B --> C[Buka detail tugas]
    C --> D{Status booking?}
    D -->|confirmed| E[Klik 'Mulai Tugas']
    E --> F[status=ongoing<br/>car.status=rented<br/>driver.status=on_duty]
    F --> G[Antar/jemput pelanggan]
    D -->|ongoing| G
    G --> H[Upload bukti pengantaran<br/>foto jpeg/jpg/png max 5MB]
    H --> I{Validasi foto?}
    I -->|Gagal| H
    I -->|Lolos| J[Simpan delivery_proof<br/>status TETAP ongoing]
    J --> K[Menunggu konfirmasi admin]
    K --> L[Admin set status=completed]
    L --> M[car.status=available<br/>driver.status=available]
    M --> Z([Selesai])
```

## 6. Update Status Booking oleh Admin

```mermaid
flowchart TD
    A([Mulai]) --> B[Admin pilih status baru]
    B --> C{Status lama completed<br/>atau cancelled?}
    C -->|Ya & status berubah| D[Error: status final<br/>tidak dapat diubah]
    C -->|Tidak| E[Update booking.status]
    E --> F{Status baru?}
    F -->|completed / cancelled| G[car.status=available<br/>driver.status=available jika ada]
    F -->|ongoing & sebelumnya bukan ongoing| H[car.status=rented<br/>driver.status=on_duty jika ada]
    F -->|lainnya| I[Tidak ubah status mobil/driver]
    G --> J[Sukses]
    H --> J
    I --> J
    J --> Z([Selesai])
    D --> Z
```
