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

    // Relations de paiements
    public function clientPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'client_id');
    }

    public function providerPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'provider_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get reviews received by this user
     */
    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewed_id');
    }

    /**
     * Get reviews written by this user
     */
    public function reviewsWritten(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Get approved reviews received
     */
    public function approvedReviewsReceived(): HasMany
    {
        return $this->reviewsReceived()->where('status', 'approved');
    }

    /**
     * Calculate and update user's average rating based on approved reviews
     */
    public function updateAverageRating(): void
    {
        $averageRating = Review::where('reviewed_id', $this->id)
            ->where('status', 'approved')
            ->avg('overall_rating');

        // Mettre à jour le rating dans le profil utilisateur
        if ($this->profile) {
            $this->profile->update([
                'rating' => $averageRating ? round($averageRating, 2) : null,
            ]);
        }
    }

    /**
     * Check if user can review a specific booking
     */
    public function canReview($booking): bool
    {
        // Must be either client or provider of the booking
        if ($this->id !== $booking->client_id && $this->id !== $booking->provider_id) {
            return false;
        }

        // Booking must be completed
        if ($booking->status !== 'completed') {
            return false;
        }

        // Must not have already reviewed this booking
        return !Review::where('booking_request_id', $booking->id)
            ->where('reviewer_id', $this->id)
            ->exists();
    }

    /**
     * Get review statistics for this user
     */
    public function getReviewStats(): array
    {
        $receivedReviews = $this->approvedReviewsReceived();
        
        return [
            'total_reviews' => $receivedReviews->count(),
            'average_rating' => $receivedReviews->avg('overall_rating'),
            'rating_distribution' => [
                5 => $receivedReviews->where('overall_rating', 5)->count(),
                4 => $receivedReviews->where('overall_rating', 4)->count(),
                3 => $receivedReviews->where('overall_rating', 3)->count(),
                2 => $receivedReviews->where('overall_rating', 2)->count(),
                1 => $receivedReviews->where('overall_rating', 1)->count(),
            ],
            'detailed_ratings' => [
                'quality' => $receivedReviews->whereNotNull('quality_rating')->avg('quality_rating'),
                'communication' => $receivedReviews->whereNotNull('communication_rating')->avg('communication_rating'),
                'punctuality' => $receivedReviews->whereNotNull('punctuality_rating')->avg('punctuality_rating'),
                'professionalism' => $receivedReviews->whereNotNull('professionalism_rating')->avg('professionalism_rating'),
                'value' => $receivedReviews->whereNotNull('value_rating')->avg('value_rating'),
            ],
        ];
    }
}
