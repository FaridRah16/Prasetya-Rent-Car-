<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Kontak Bisnis
    |--------------------------------------------------------------------------
    |
    | Nomor WhatsApp resmi yang dipakai tombol "Hubungi Kami" di halaman
    | publik. Diisi lewat env BUSINESS_WHATSAPP (format internasional tanpa
    | tanda '+', mis. 6281234567890).
    |
    */

    'whatsapp' => env('BUSINESS_WHATSAPP', '6281234567890'),

    /*
    |--------------------------------------------------------------------------
    | Batas Waktu Booking Pending (jam)
    |--------------------------------------------------------------------------
    |
    | Booking berstatus 'pending' yang belum dibayar akan otomatis dibatalkan
    | (lewat command bookings:expire-pending) setelah melewati jumlah jam ini,
    | dan tidak lagi mengunci tanggal pada pengecekan bentrok.
    |
    */

    'pending_ttl_hours' => (int) env('BOOKING_PENDING_TTL_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Batas Waktu Pembayaran (menit)
    |--------------------------------------------------------------------------
    |
    | Setelah booking dibuat, customer diberi jendela waktu ini (dalam menit)
    | untuk mengunggah bukti pembayaran. Hitung mundur realtime ditampilkan
    | pada nota. Jika lewat, booking otomatis dibatalkan (saat dibuka kembali
    | atau lewat command bookings:expire-pending).
    |
    */

    'payment_window_minutes' => (int) env('BOOKING_PAYMENT_WINDOW_MINUTES', 30),

];
