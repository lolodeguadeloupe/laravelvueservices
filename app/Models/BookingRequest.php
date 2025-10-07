<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class BookingRequest extends Model
{
    protected $fillable = [
        'uuid',
        'service_id',
        'client_id',
        'provider_id',
        'status',
        'preferred_datetime',
        'confirmed_datetime',
        'client_address',
        'client_notes',
        'provider_notes',
        'quoted_price',
        'final_price',
        'estimated_duration',
        'accepted_at',
        'rejected_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'preferred_datetime' => 'datetime',
        'confirmed_datetime' => 'datetime',
        'client_address' => 'array',
        'quoted_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'estimated_duration' => 'integer',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (BookingRequest $booking) {
            if (empty($booking->uuid)) {
                $booking->uuid = Str::uuid();
            }
        });
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function clientReview(): HasOne
    {
        return $this->hasOne(Review::class)->where('reviewer_type', 'client');
    }

    public function providerReview(): HasOne
    {
        return $this->hasOne(Review::class)->where('reviewer_type', 'provider');
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeForProvider($query, int $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeAccepted(): bool
    {
        return $this->isPending();
    }

    public function canBeRejected(): bool
    {
        return $this->isPending();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'accepted']);
    }

    public function canBeCompleted(): bool
    {
        return $this->isAccepted();
    }

    public function getFormattedAddressAttribute(): string
    {
        if (! $this->client_address) {
            return '';
        }

        $parts = array_filter([
            $this->client_address['street'] ?? null,
            $this->client_address['city'] ?? null,
            $this->client_address['postal_code'] ?? null,
        ]);

        return implode(', ', $parts);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'accepted' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'accepted' => 'Accepté',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            'rejected' => 'Refusé',
            default => 'Inconnu',
        };
    }
}
