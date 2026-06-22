<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// CATATAN KEAMANAN: 'role', 'verification_status', dan 'verified_at' SENGAJA
// tidak mass-assignable untuk mencegah privilege-escalation / self-verify.
// Field tersebut hanya boleh di-set eksplisit (mis. $user->role = ...).
#[Fillable(['name', 'email', 'password', 'phone', 'whatsapp_number', 'avatar', 'sim_photo'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Jaga invariant: verified_at selalu konsisten dengan verification_status.
     * Saat status 'verified' tapi belum ada timestamp → diisi sekarang; saat
     * status bukan 'verified' → timestamp dikosongkan. Mencegah kondisi tak
     * konsisten (mis. status verified tanpa verified_at).
     */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->verification_status === 'verified') {
                if (is_null($user->verified_at)) {
                    $user->verified_at = now();
                }
            } else {
                $user->verified_at = null;
            }
        });
    }

    /**
     * Get the bookings for the user.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the driver profile for the user.
     */
    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    /**
     * Get bookings where user is assigned as driver.
     */
    public function bookingsAsDriver(): HasMany
    {
        return $this->hasMany(Booking::class, 'driver_id');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user is driver.
     */
    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    /**
     * Check if the account has been verified by admin.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Check if the account verification is awaiting admin confirmation.
     */
    public function isPendingVerification(): bool
    {
        return $this->verification_status === 'pending';
    }

    /**
     * Check if the account has not been verified yet.
     */
    public function isUnverified(): bool
    {
        return $this->verification_status === 'unverified';
    }
}
