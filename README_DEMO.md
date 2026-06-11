# 🚗 Prasetya Rent Car - Demo Guide

## ✅ Status: READY TO TEST!

Server sudah berjalan di: **http://127.0.0.1:8000**

---

## 🔑 Kredensial Login

### Admin
- **Email**: admin@prasetyarentcar.com
- **Password**: password
- **Akses**: Dashboard admin, kelola mobil, user, booking, laporan

### Driver
- **Email**: budi@driver.com atau agus@driver.com
- **Password**: password
- **Akses**: Dashboard driver, lihat tugas, update status

### Customer
- **Email**: siti@customer.com, andi@customer.com, atau dewi@customer.com
- **Password**: password
- **Akses**: Dashboard customer, booking mobil, riwayat

---

## 📋 Fitur Yang Sudah Berfungsi

### ✅ Database & Backend
- [x] 7 tabel berhasil dibuat dan dimigrate
- [x] 5 model dengan relasi lengkap (User, Car, Driver, Booking, Review)
- [x] Data contoh: 1 admin, 2 driver, 3 customer, 5 mobil
- [x] Middleware role-based access control

### ✅ Authentication
- [x] Login dengan redirect sesuai role
- [x] Register untuk customer
- [x] Logout
- [x] Form validation dalam Bahasa Indonesia

### ✅ Halaman Public
- [x] Homepage dengan hero section, fitur, dan CTA
- [x] Katalog mobil dengan filter (brand, tipe, harga)
- [x] Search mobil
- [x] Pagination
- [x] Navbar responsive

### ✅ Dashboard
- [x] Admin dashboard dengan stat cards
- [x] Customer dashboard dengan ringkasan booking
- [x] Driver dashboard dengan tugas aktif
- [x] Sidebar navigation untuk semua role
- [x] Mobile-friendly sidebar

---

## 🎨 Desain
- Warna utama: Biru tua (#1a3c5e) + Kuning/Oranye (#f5a623)
- Bootstrap 5 + Bootstrap Icons
- Responsive untuk mobile & desktop
- Smooth transitions & hover effects

---

## 🚀 Cara Testing

1. **Buka browser** dan akses: http://127.0.0.1:8000

2. **Test Halaman Public**:
   - Homepage: lihat hero section dan fitur
   - Klik "Lihat Katalog" atau menu "Katalog Mobil"
   - Test filter brand, tipe, search
   - Klik "Lihat Detail" pada salah satu mobil (akan error karena belum dibuat)

3. **Test Login**:
   - Klik "Masuk" di navbar
   - Login sebagai **customer**: siti@customer.com / password
   - Akan redirect ke customer dashboard
   - Logout dan coba login sebagai **admin**: admin@prasetyarentcar.com / password
   - Akan redirect ke admin dashboard

4. **Test Register**:
   - Klik "Daftar" di navbar
   - Isi form registrasi
   - Otomatis login dan masuk customer dashboard

---

## 📝 Yang Perlu Dikerjakan Selanjutnya

### Prioritas Tinggi
1. **Detail Mobil** - Halaman show car dengan tombol "Sewa Sekarang"
2. **Booking Flow Customer** - Form booking mobil
3. **Upload Bukti Bayar** - Customer upload payment proof
4. **Admin: Kelola Mobil** - CRUD mobil dengan upload foto
5. **Admin: Kelola Booking** - Konfirmasi, assign driver, update status

### Prioritas Medium
6. **Driver: Kelola Tugas** - List tugas dan update status
7. **Customer: Riwayat Booking** - List booking dengan detail
8. **Customer: Profile** - Edit profil dan ganti password
9. **Admin: Kelola User** - CRUD user (tambah driver, dll)
10. **Halaman Kontak & Tentang Kami**

### Prioritas Rendah
11. **Admin: Laporan** - Dashboard chart dan export
12. **Review System** - Customer kasih review setelah selesai
13. **Email Notification** - Konfirmasi booking via email

---

## 🔧 Troubleshooting

### Server Tidak Jalan
```bash
C:\laragon\bin\php\php-8.4.20-Win32-vs17-x64\php.exe artisan serve
```

### Database Error
```bash
# Cek .env, pastikan DB_DATABASE=prasetya_rentcar
# Migrate ulang jika perlu:
C:\laragon\bin\php\php-8.4.20-Win32-vs17-x64\php.exe artisan migrate:fresh --seed
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 📞 Support

Jika ada error atau pertanyaan, capture screenshot error dan tanyakan!

**Happy Testing! 🎉**
