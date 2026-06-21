<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'brand', 'type', 'year', 'color', 'plate_number', 'price_per_day', 'status', 'image', 'gallery', 'seats', 'transmission', 'fuel', 'description'])]
class Car extends Model
{
    use HasFactory;

    protected $casts = [
        'gallery' => 'array',
    ];

    /**
     * Get the bookings for the car.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the booking that is currently using this car (status ongoing).
     * Used to display "Disewa s/d <tanggal>" in the public catalog.
     */
    public function activeBooking(): HasOne
    {
        return $this->hasOne(Booking::class)
            ->where('status', 'ongoing')
            ->latest('end_date');
    }

    /**
     * Check if car is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if car is rented.
     */
    public function isRented(): bool
    {
        return $this->status === 'rented';
    }

    /**
     * Check if car is under maintenance.
     */
    public function isMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    /**
     * Scope a query to only include available cars.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Get the car's image URL.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-car.png');
    }

    /**
     * Get the car's gallery image URLs.
     */
    public function getGalleryUrlsAttribute(): array
    {
        $images = $this->gallery;
        if (empty($images) || !is_array($images)) {
            return [
                $this->image_url,
                $this->image_url,
                $this->image_url,
                $this->image_url,
            ];
        }
        return array_merge([$this->image_url], array_map(function($img) {
            return asset('storage/' . $img);
        }, $images));
    }
}
