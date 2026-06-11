<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'license_number', 'status'])]
class Driver extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the driver.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bookings for the driver.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'driver_id', 'user_id');
    }

    /**
     * Check if driver is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if driver is on duty.
     */
    public function isOnDuty(): bool
    {
        return $this->status === 'on_duty';
    }

    /**
     * Scope a query to only include available drivers.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
