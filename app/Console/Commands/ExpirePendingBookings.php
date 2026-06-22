<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class ExpirePendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan booking pending yang belum dibayar dan sudah melewati batas waktu (TTL).';

    /**
     * Execute the console command.
     *
     * Booking 'pending' tidak pernah mengunci status mobil/driver (itu baru
     * terjadi saat 'ongoing'), jadi pembatalan cukup mengubah status booking.
     */
    public function handle(): int
    {
        // Batas waktu pembayaran (menit) adalah jendela yang mengikat: booking
        // pending yang belum dibayar melewatinya akan dibatalkan otomatis.
        $windowMinutes = (int) config('business.payment_window_minutes', 30);
        $cutoff = now()->subMinutes($windowMinutes);

        $count = Booking::where('status', 'pending')
            ->where('payment_status', 'unpaid')
            ->where('created_at', '<', $cutoff)
            ->update(['status' => 'cancelled']);

        $this->info("{$count} booking pending kedaluwarsa (batas waktu pembayaran {$windowMinutes} menit) dibatalkan.");

        return self::SUCCESS;
    }
}
