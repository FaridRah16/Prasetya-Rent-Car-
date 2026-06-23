<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Midtrans order_id unik untuk setiap booking
            // Format: "BOOKING-{id}-{timestamp}" agar tidak bentrok
            $table->string('order_id')->nullable()->unique()->after('id');
            // Snap token untuk membuka halaman pembayaran Midtrans
            $table->string('snap_token')->nullable()->after('order_id');
            // Tipe pembayaran yang dipakai: bank_transfer, gopay, qris, dll.
            $table->string('payment_type')->nullable()->after('snap_token');
            // Channel spesifik: bca, bni, bri, gopay, qris, dll.
            $table->string('payment_channel')->nullable()->after('payment_type');
            // Status transaksi dari Midtrans
            $table->string('transaction_status')->nullable()->after('payment_channel');
            // Waktu transaksi dibuat di Midtrans
            $table->timestamp('transaction_time')->nullable()->after('transaction_status');
            // Waktu pembayaran selesai (settlement)
            $table->timestamp('settlement_time')->nullable()->after('transaction_time');
            // Gross amount yang diterima Midtrans (untuk rekonsiliasi)
            $table->decimal('gross_amount', 10, 2)->nullable()->after('settlement_time');
            // Raw JSON response dari Midtrans callback (untuk audit trail)
            $table->json('midtrans_response')->nullable()->after('gross_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'order_id',
                'snap_token',
                'payment_type',
                'payment_channel',
                'transaction_status',
                'transaction_time',
                'settlement_time',
                'gross_amount',
                'midtrans_response',
            ]);
        });
    }
};
