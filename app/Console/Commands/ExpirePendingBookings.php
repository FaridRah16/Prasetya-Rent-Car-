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
        $ttlHours = (int) config('business.pending_ttl_hours', 24);
        $cutoff = now()->subHours($ttlHours);

        $count = Booking::where('status', 'pending')
            ->where('payment_status', 'unpaid')
            ->where('created_at', '<', $cutoff)
            ->update(['status' => 'cancelled']);

        $this->info("{$count} booking pending kedaluwarsa dibatalkan.");

        return self::SUCCESS;
    }
}
