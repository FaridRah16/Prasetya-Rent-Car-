<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Indeks untuk mempercepat query yang sering dipakai: pengecekan
     * bentrok booking (car_id/driver_id + status + rentang tanggal) dan
     * filter daftar booking berdasarkan status/payment_status.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['car_id', 'status'], 'bookings_car_status_index');
            $table->index(['driver_id', 'status'], 'bookings_driver_status_index');
            $table->index('status', 'bookings_status_index');
            $table->index('payment_status', 'bookings_payment_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_car_status_index');
            $table->dropIndex('bookings_driver_status_index');
            $table->dropIndex('bookings_status_index');
            $table->dropIndex('bookings_payment_status_index');
        });
    }
};
