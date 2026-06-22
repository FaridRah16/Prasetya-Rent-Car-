<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan booking pending yang melewati batas waktu pembayaran.
// Dijalankan tiap menit agar slot mobil/driver bebas tepat waktu (jendela 30 menit).
Schedule::command('bookings:expire-pending')->everyMinute();
