<?php

namespace App\Models;

use App\Notifications\BookingStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class BookingRequest extends Model
{
    use HasFactory;

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
        'started_at',
        'finished_at',
        'intervention_report',
        'client_signature',
        'provider_location',
        'work_summary',
        'before_photos',
        'after_photos',
        'requires_follow_up',
        'follow_up_date',
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
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'intervention_report' => 'array',
        'client_signature' => 'array',
        'provider_location' => 'array',
        'before_photos' => 'array',
        'after_photos' => 'array',
        'requires_follow_up' => 'boolean',
        'follow_up_date' => 'date',
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
        return $this->isPending() || $this->status === 'quoted';
    }

    public function canBeRejected(): bool
    {
        return $this->isPending() || $this->status === 'quoted';
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
            'quoted' => 'Devis envoyé',
            'accepted' => 'Accepté',
            'in_progress' => 'En cours',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
            'rejected' => 'Refusé',
            default => 'Inconnu',
        };
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    public function unreadMessagesFor(User $user): int
    {
        return $this->messages()
            ->where('receiver_id', $user->id)
            ->unread()
            ->count();
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isQuoted(): bool
    {
        return $this->status === 'quoted';
    }

    public function canBeStarted(): bool
    {
        return $this->isAccepted() && ! $this->started_at;
    }

    public function canBeFinished(): bool
    {
        return $this->isInProgress() && $this->started_at && ! $this->finished_at;
    }

    public function processPayment(): array
    {
        $commissionService = app(\App\Services\CommissionService::class);

        return $commissionService->processPayment($this);
    }

    public function confirmPayment(): void
    {
        $commissionService = app(\App\Services\CommissionService::class);
        $commissionService->confirmPayment($this);
    }

    public function createDispute(User $reportedBy, string $type, string $description, array $evidence = [], ?float $disputedAmount = null): Dispute
    {
        $reportedAgainst = $reportedBy->id === $this->client_id ? $this->provider : $this->client;

        return Dispute::create([
            'booking_request_id' => $this->id,
            'reported_by' => $reportedBy->id,
            'reported_against' => $reportedAgainst->id,
            'type' => $type,
            'description' => $description,
            'evidence' => $evidence,
            'disputed_amount' => $disputedAmount ?? $this->final_price ?? $this->quoted_price,
        ]);
    }

    public function getDurationInMinutes(): ?int
    {
        if (! $this->started_at || ! $this->finished_at) {
            return null;
        }

        return $this->started_at->diffInMinutes($this->finished_at);
    }

    public function hasInterventionReport(): bool
    {
        return ! empty($this->intervention_report);
    }

    public function logStatusChange(string $newStatus, User $user, ?string $reason = null, ?array $metadata = null): void
    {
        $oldStatus = $this->status;

        BookingStatusHistory::logStatusChange(
            $this,
            $oldStatus,
            $newStatus,
            $user,
            $reason,
            $metadata
        );

        // Créer un message système automatique
        $statusMessages = [
            'pending' => 'Demande créée',
            'quoted' => 'Devis envoyé par le prestataire',
            'accepted' => 'Demande acceptée par le prestataire',
            'in_progress' => 'Intervention commencée',
            'completed' => 'Intervention terminée',
            'cancelled' => 'Réservation annulée',
            'rejected' => 'Demande refusée',
        ];

        if (isset($statusMessages[$newStatus])) {
            Message::createSystemMessage(
                $this,
                $statusMessages[$newStatus],
                $metadata
            );
        }

        // Envoyer notification selon le destinataire
        $this->sendStatusNotification($newStatus, $user);
    }

    private function sendStatusNotification(string $newStatus, User $changedBy): void
    {
        // Déterminer qui doit recevoir la notification
        $recipient = null;

        if ($changedBy->id === $this->provider_id) {
            // Le prestataire a fait le changement, notifier le client
            $recipient = $this->client;
        } elseif ($changedBy->id === $this->client_id) {
            // Le client a fait le changement, notifier le prestataire
            $recipient = $this->provider;
        }

        if ($recipient) {
            $recipient->notify(new BookingStatusChanged($this, $newStatus));
        }
    }
}
