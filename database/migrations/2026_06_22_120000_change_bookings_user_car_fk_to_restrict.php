<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah perilaku onDelete FK bookings dari 'cascade' menjadi 'restrict'.
     *
     * Alasan: dengan cascade, menghapus 1 user/mobil ikut menghapus SELURUH
     * riwayat booking-nya (termasuk yang sudah selesai & dibayar) — menghancurkan
     * jejak finansial/audit. Dengan restrict, entitas yang masih punya riwayat
     * booking tidak bisa dihapus, sehingga data historis terlindungi.
     *
     * Kolom driver_id tetap 'set null' (booking boleh kehilangan driver).
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['car_id']);

            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['car_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('car_id')->references('id')->on('cars')->cascadeOnDelete();
        });
    }
};
