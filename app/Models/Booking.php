<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'car_id', 'driver_id', 'start_date', 'pickup_time', 'end_date', 'return_time', 'total_days', 'total_price', 'pickup_location', 'pickup_lat', 'pickup_lng', 'dropoff_location', 'dropoff_lat', 'dropoff_lng', 'status', 'payment_status', 'payment_proof', 'delivery_proof', 'notes'])]
class Booking extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the car that owns the booking.
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Get the driver for the booking.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Scope booking yang masih "menahan" sebuah slot (mobil/driver):
     * 'confirmed' & 'ongoing', plus 'pending' yang belum kedaluwarsa (TTL).
     * Pending yang sudah lewat batas waktu tidak lagi memblokir tanggal.
     */
    public function scopeBlockingSlot($query)
    {
        $ttlHours = (int) config('business.pending_ttl_hours', 24);

        return $query->where(function ($q) use ($ttlHours) {
            $q->whereIn('status', ['confirmed', 'ongoing'])
              ->orWhere(function ($q2) use ($ttlHours) {
                  $q2->where('status', 'pending')
                     ->where('created_at', '>=', now()->subHours($ttlHours));
              });
        });
    }

    /**
     * Check if booking is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if booking is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if booking is ongoing.
     */
    public function isOngoing(): bool
    {
        return $this->status === 'ongoing';
    }

    /**
     * Check if booking is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if booking is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if payment is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if payment is unpaid.
     */
    public function isUnpaid(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    /**
     * Scope a query to only include pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include ongoing bookings.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    /**
     * Get the payment proof URL.
     */
    public function getPaymentProofUrlAttribute(): ?string
    {
        return $this->payment_proof ? asset('storage/' . $this->payment_proof) : null;
    }

    /**
     * Batas waktu pembayaran: dibuat + jendela waktu (menit) dari config.
     * Mengembalikan null jika booking tidak lagi menunggu pembayaran
     * (status bukan 'pending' atau sudah dibayar).
     */
    public function paymentDeadline(): ?\Carbon\Carbon
    {
        if ($this->status !== 'pending' || $this->payment_status !== 'unpaid') {
            return null;
        }

        $minutes = (int) config('business.payment_window_minutes', 30);

        return $this->created_at->copy()->addMinutes($minutes);
    }

    /**
     * Apakah batas waktu pembayaran sudah terlewati?
     */
    public function isPaymentExpired(): bool
    {
        $deadline = $this->paymentDeadline();

        return $deadline !== null && now()->greaterThan($deadline);
    }
}
