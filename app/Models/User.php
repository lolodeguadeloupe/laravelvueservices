<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'is_active',
        'last_active_at',
        'phone',
        'address',
        'verification_status',
        'rejection_reason',
        'phone_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_active_at' => 'datetime',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function providedServices(): HasMany
    {
        return $this->hasMany(Service::class, 'provider_id');
    }

    public function clientBookings(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'client_id');
    }

    public function providerBookings(): HasMany
    {
        return $this->hasMany(BookingRequest::class, 'provider_id');
    }

    // Type checks
    public function isClient(): bool
    {
        return $this->user_type === 'client';
    }

    public function isProvider(): bool
    {
        return $this->user_type === 'provider';
    }

    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeClients($query)
    {
        return $query->where('user_type', 'client');
    }

    public function scopeProviders($query)
    {
        return $query->where('user_type', 'provider');
    }

    public function scopeAdmins($query)
    {
        return $query->where('user_type', 'admin');
    }

    // Helper methods
    public function getDisplayNameAttribute(): string
    {
        if ($this->profile && $this->profile->full_name) {
            return $this->profile->full_name;
        }

        return $this->name;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->profile?->avatar;
    }

    public function updateLastActive(): void
    {
        $this->update(['last_active_at' => now()]);
    }

    // Provider-specific methods
    public function canProvideService(): bool
    {
        return $this->isProvider() && $this->is_active;
    }

    public function getProviderRating(): float
    {
        return $this->profile?->rating ?? 0;
    }

    public function getTotalBookingsCount(): int
    {
        return $this->providerBookings()->completed()->count();
    }
}
