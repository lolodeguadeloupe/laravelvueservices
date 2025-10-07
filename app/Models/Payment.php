<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'booking_request_id',
        'client_id',
        'provider_id',
        'amount',
        'platform_fee',
        'provider_amount',
        'currency',
        'status',
        'payment_method',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_metadata',
        'paid_at',
        'refunded_at',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'provider_amount' => 'decimal:2',
            'stripe_metadata' => 'array',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Payment $payment) {
            if (empty($payment->uuid)) {
                $payment->uuid = Str::uuid();
            }
        });
    }

    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function calculatePlatformFee(float $rate = 0.15): void
    {
        $this->platform_fee = $this->amount * $rate;
        $this->provider_amount = $this->amount - $this->platform_fee;
    }
}
