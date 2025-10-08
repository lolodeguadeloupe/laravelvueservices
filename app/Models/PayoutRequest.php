<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PayoutRequest extends Model
{
    /** @use HasFactory<\Database\Factories\PayoutRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'wallet_id',
        'amount',
        'currency',
        'status',
        'payout_method',
        'bank_details',
        'stripe_payout_id',
        'failure_reason',
        'metadata',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bank_details' => 'array',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (PayoutRequest $payout) {
            if (empty($payout->uuid)) {
                $payout->uuid = Str::uuid();
            }
            if (empty($payout->status)) {
                $payout->status = 'pending';
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function markAsProcessed(?string $stripePayoutId = null): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
            'stripe_payout_id' => $stripePayoutId,
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);

        // Remettre les fonds gelés dans le solde disponible
        $this->wallet->unfreezeFunds($this->amount);
    }

    public function canBeProcessed(): bool
    {
        return $this->status === 'pending' && 
               $this->wallet->frozen_balance >= $this->amount;
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }
}
