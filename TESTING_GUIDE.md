# 🧪 PANDUAN TESTING PRASETYA RENT CAR

## 📋 Checklist Testing - User Management & Driver Tasks

### ✅ Admin - User Management

#### 1. User List Page
- [ ] Buka `http://127.0.0.1:8000/admin/users`
- [ ] Verifikasi semua user ditampilkan
- [ ] Test filter by role (Admin/Customer/Driver)
- [ ] Test search by name/email/phone
- [ ] Verifikasi pagination works

#### 2. Create User
- [ ] Klik tombol "Tambah User"
- [ ] Isi form untuk role **Customer**
  - Verifikasi license field TIDAK muncul
  - Submit dan verifikasi berhasil
- [ ] Klik tombol "Tambah User" lagi
- [ ] Isi form untuk role **Driver**
  - Verifikasi license field MUNCUL
  - Isi nomor SIM
  - Submit dan verifikasi driver record dibuat
- [ ] Verifikasi validation error jika:
  - Email sudah terdaftar
  - Password < 8 karakter
  - Password confirmation tidak match
  - License number kosong untuk driver

#### 3. Edit User
- [ ] Klik tombol edit pada user manapun
- [ ] Ubah nama dan phone
- [ ] Kosongkan password (should not update password)
- [ ] Submit dan verifikasi perubahan
- [ ] Edit lagi dengan isi password baru
- [ ] Submit dan coba login dengan password baru
- [ ] Edit user dengan role Customer → Driver
  - Verifikasi license field muncul
  - Isi license number
  - Verifikasi driver record dibuat
- [ ] Edit user dengan role Driver → Customer
  - Verifikasi driver record dihapus

#### 4. Show User Detail
- [ ] Klik nama user dengan role **Customer**
  - Verifikasi statistik booking ditampilkan
  - Verifikasi tabel riwayat booking ditampilkan
  - Verifikasi total transaksi dihitung
- [ ] Klik nama user dengan role **Driver**
  - Verifikasi statistik tugas ditampilkan
  - Verifikasi status driver (available/on_duty)
  - Verifikasi tabel riwayat tugas ditampilkan
- [ ] Klik nama user dengan role **Admin**
  - Verifikasi halaman menampilkan info admin

#### 5. Delete User
- [ ] Coba delete user sendiri (should fail)
- [ ] Coba delete customer dengan active booking (should fail)
- [ ] Delete customer tanpa active booking (should success)

---

### ✅ Driver - Task Management

#### 1. Driver Dashboard
- [ ] Login sebagai driver: `budi@driver.com / password`
- [ ] Verifikasi dashboard menampilkan:
  - Total tugas aktif
  - Total tugas selesai
  - Status driver (Tersedia/Bertugas)
- [ ] Verifikasi tabel tugas terbaru ditampilkan

#### 2. Task List (Active)
- [ ] Klik menu "Tugas Saya"
- [ ] Verifikasi hanya booking dengan status confirmed/ongoing yang ditampilkan
- [ ] Verifikasi hanya booking yang assigned ke driver login
- [ ] Klik "Lihat Detail" pada salah satu task

#### 3. Task Detail Page
- [ ] Verifikasi informasi customer ditampilkan (nama, phone, email)
- [ ] Verifikasi detail mobil ditampilkan
- [ ] Verifikasi tanggal mulai dan selesai ditampilkan
- [ ] Verifikasi lokasi penjemputan dan pengantaran ditampilkan
- [ ] Verifikasi catatan customer (jika ada)
- [ ] Verifikasi info pembayaran (total & status)

#### 4. Start Task
- [ ] Pada task dengan status "Confirmed"
- [ ] Klik tombol "Mulai Tugas"
- [ ] Verifikasi status berubah menjadi "Ongoing"
- [ ] Verifikasi tombol berubah menjadi "Selesaikan Tugas"

#### 5. Complete Task
- [ ] Pada task dengan status "Ongoing"
- [ ] Klik tombol "Selesaikan Tugas"
- [ ] Confirm dialog
- [ ] Verifikasi:
  - Status booking → completed
  - Car status → available
  - Driver status → available
  - Redirect ke task list
  - Task tidak muncul di active tasks
  - Task muncul di history

#### 6. Task History
- [ ] Klik menu "Riwayat Tugas"
- [ ] Verifikasi task yang completed/cancelled ditampilkan
- [ ] Verifikasi pagination works

#### 7. Contact Customer Features
- [ ] Di task detail page
- [ ] Verifikasi tombol "Telepon" dengan link tel:
- [ ] Verifikasi tombol "WhatsApp" dengan link wa.me

---

### ✅ Integration Test - Complete Flow

#### Skenario: Admin Assign Driver → Driver Complete Task

1. **Admin** (admin@prasetyarentcar.com):
   - [ ] Login ke admin panel
   - [ ] Buka "Kelola Pemesanan"
   - [ ] Pilih booking dengan status "Pending" dan payment "Paid"
   - [ ] Assign driver "Budi"
   - [ ] Update status booking → "Confirmed"
   - [ ] Verifikasi driver status → "on_duty"

2. **Driver** (budi@driver.com):
   - [ ] Login ke driver dashboard
   - [ ] Verifikasi task baru muncul di "Tugas Saya"
   - [ ] Klik detail task
   - [ ] Klik "Mulai Tugas"
   - [ ] Verifikasi status → "Ongoing"
   - [ ] Klik "Selesaikan Tugas"
   - [ ] Verifikasi:
     - Status booking → "Completed"
     - Car status → "Available"
     - Driver status → "Available"
     - Task pindah ke history

3. **Admin** (verifikasi):
   - [ ] Buka booking detail
   - [ ] Verifikasi status "Completed"
   - [ ] Buka car detail
   - [ ] Verifikasi status "Available"
   - [ ] Buka driver detail (user show)
   - [ ] Verifikasi status "Available"
   - [ ] Verifikasi task muncul di riwayat driver

---

## 🐛 Known Issues / Edge Cases

### Edge Case 1: Driver with Active Task
- [ ] Test: Coba delete driver yang sedang bertugas
- [ ] Expected: Should block deletion (perlu implementasi jika belum)

### Edge Case 2: Reassign Driver
- [ ] Test: Admin reassign driver dari Budi ke Agus
- [ ] Expected: Status Budi → available, status Agus → on_duty

### Edge Case 3: Cancel Booking with Driver
- [ ] Test: Admin cancel booking yang sudah assigned driver
- [ ] Expected: Driver status → available, car status → available

---

## 📝 Hasil Testing

| Fitur | Status | Catatan |
|-------|--------|---------|
| Admin User List | ⬜ | |
| Admin Create User | ⬜ | |
| Admin Edit User | ⬜ | |
| Admin Show User | ⬜ | |
| Admin Delete User | ⬜ | |
| Driver Dashboard | ⬜ | |
| Driver Task List | ⬜ | |
| Driver Task Detail | ⬜ | |
| Driver Start Task | ⬜ | |
| Driver Complete Task | ⬜ | |
| Driver Task History | ⬜ | |
| Integration Test | ⬜ | |

**Legenda**: ⬜ Belum Test | ✅ Pass | ❌ Fail

---

## 🔄 Next Steps After Testing

Jika semua test pass, lanjut ke:
- [ ] **Fase 7**: Customer Profile (edit profile & change password)
- [ ] **Fase 8**: Review & Rating System
- [ ] **Fase 9**: Admin Reports & Analytics
- [ ] **Fase 10**: Contact & About Pages
- [ ] **Fase 11**: Polish & Responsive Check
