<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index pendukung pengecekan ketersediaan (overlap) yang dijalankan pada
     * setiap percobaan booking & penugasan driver, serta filter TTL pembayaran.
     *
     * Tanpa index ini, query overlap mem-filter rentang start_date/end_date dan
     * created_at dengan row scan saat tabel membesar.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['car_id', 'start_date', 'end_date'], 'bookings_car_dates_index');
            $table->index(['driver_id', 'start_date', 'end_date'], 'bookings_driver_dates_index');
            $table->index('created_at', 'bookings_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_car_dates_index');
            $table->dropIndex('bookings_driver_dates_index');
            $table->dropIndex('bookings_created_at_index');
        });
    }
};
