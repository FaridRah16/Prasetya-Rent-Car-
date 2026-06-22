<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah cars.transmission & cars.fuel dari string bebas menjadi enum,
     * agar nilainya konsisten (mencegah typo / beda kapitalisasi yang merusak
     * filter & pengelompokan). Nilai enum selaras dengan StoreCarRequest.
     * Tetap nullable untuk menampung baris lama yang masih NULL.
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->enum('transmission', ['Manual', 'Automatic', 'CVT'])->nullable()->change();
            $table->enum('fuel', ['Bensin', 'Diesel', 'Hybrid', 'Listrik'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('transmission')->nullable()->change();
            $table->string('fuel')->nullable()->change();
        });
    }
};
