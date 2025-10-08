<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Dispute extends Model
{
    /** @use HasFactory<\Database\Factories\DisputeFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'booking_request_id',
        'reported_by',
        'reported_against',
        'assigned_to',
        'type',
        'status',
        'description',
        'evidence',
        'disputed_amount',
        'refund_amount',
        'resolution_notes',
        'resolved_at',
    ];

    protected $casts = [
        'evidence' => 'array',
        'disputed_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'resolved_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Dispute $dispute) {
            if (empty($dispute->uuid)) {
                $dispute->uuid = Str::uuid();
            }
            if (empty($dispute->status)) {
                $dispute->status = 'open';
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class, 'booking_request_id');
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function reportedAgainst(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_against');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['open', 'investigating']);
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function canBeAssigned(): bool
    {
        return $this->isOpen() && !$this->assigned_to;
    }

    public function resolve(float $refundAmount = 0, ?string $notes = null): void
    {
        $this->update([
            'status' => 'resolved',
            'refund_amount' => $refundAmount,
            'resolution_notes' => $notes,
            'resolved_at' => now(),
        ]);

        // Traiter le remboursement si applicable
        if ($refundAmount > 0) {
            $commissionService = app(\App\Services\CommissionService::class);
            $commissionService->refundPayment($this->booking, $refundAmount);
        }
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'refund_request' => 'Demande de remboursement',
            'quality_issue' => 'Problème de qualité',
            'payment_dispute' => 'Litige de paiement',
            'behavior_complaint' => 'Plainte comportementale',
            default => 'Autre',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'open' => 'Ouvert',
            'investigating' => 'En cours d\'investigation',
            'resolved' => 'Résolu',
            'closed' => 'Fermé',
            default => 'Inconnu',
        };
    }
}
