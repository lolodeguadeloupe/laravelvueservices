<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'balance',
        'pending_balance',
        'frozen_balance',
        'currency',
        'stripe_account_info',
        'is_active',
        'last_payout_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'frozen_balance' => 'decimal:2',
        'stripe_account_info' => 'array',
        'is_active' => 'boolean',
        'last_payout_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Wallet $wallet) {
            if (empty($wallet->uuid)) {
                $wallet->uuid = Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(PayoutRequest::class);
    }

    public function getTotalBalanceAttribute(): float
    {
        return $this->balance + $this->pending_balance;
    }

    public function getAvailableBalanceAttribute(): float
    {
        return $this->balance - $this->frozen_balance;
    }

    public function canWithdraw(float $amount): bool
    {
        $available = (float) $this->getAvailableBalanceAttribute();
        return $this->is_active && $available >= $amount;
    }

    public function addFunds(float $amount, string $type = 'earning', ?string $description = null): void
    {
        $this->increment('pending_balance', $amount);

        Transaction::create([
            'user_id' => $this->user_id,
            'wallet_id' => $this->id,
            'type' => $type,
            'amount' => $amount,
            'currency' => $this->currency ?? 'EUR',
            'status' => 'pending',
            'description' => $description ?? "Ajout de fonds ({$type})",
        ]);
    }

    public function confirmFunds(float $amount): void
    {
        if ($this->pending_balance >= $amount) {
            $this->decrement('pending_balance', $amount);
            $this->increment('balance', $amount);

            // Mettre à jour les transactions correspondantes
            $this->transactions()
                ->where('status', 'pending')
                ->where('amount', $amount)
                ->orderBy('created_at')
                ->first()
                ?->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                ]);
        }
    }

    public function freezeFunds(float $amount, string $reason = 'Litige'): bool
    {
        if ($this->balance >= $amount) {
            $this->decrement('balance', $amount);
            $this->increment('frozen_balance', $amount);

            Transaction::create([
                'user_id' => $this->user_id,
                'wallet_id' => $this->id,
                'type' => 'freeze',
                'amount' => $amount,
                'currency' => $this->currency ?? 'EUR',
                'status' => 'completed',
                'description' => "Fonds gelés: {$reason}",
                'processed_at' => now(),
            ]);

            return true;
        }

        return false;
    }

    public function unfreezeFunds(float $amount): bool
    {
        if ($this->frozen_balance >= $amount) {
            $this->decrement('frozen_balance', $amount);
            $this->increment('balance', $amount);

            Transaction::create([
                'user_id' => $this->user_id,
                'wallet_id' => $this->id,
                'type' => 'unfreeze',
                'amount' => $amount,
                'currency' => $this->currency ?? 'EUR',
                'status' => 'completed',
                'description' => 'Fonds dégelés',
                'processed_at' => now(),
            ]);

            return true;
        }

        return false;
    }

    public function withdraw(float $amount, array $payoutDetails = []): PayoutRequest
    {
        if (!$this->canWithdraw($amount)) {
            throw new \InvalidArgumentException('Solde insuffisant pour ce retrait');
        }

        // Geler les fonds pendant le traitement
        $this->freezeFunds($amount, 'Demande de retrait en cours');

        return PayoutRequest::create([
            'user_id' => $this->user_id,
            'wallet_id' => $this->id,
            'amount' => $amount,
            'currency' => $this->currency,
            'payout_method' => $payoutDetails['method'] ?? 'bank_transfer',
            'bank_details' => $payoutDetails['bank_details'] ?? null,
            'metadata' => $payoutDetails['metadata'] ?? null,
        ]);
    }
}
