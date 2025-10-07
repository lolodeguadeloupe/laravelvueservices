<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'booking_request_id',
        'reviewer_id',
        'reviewed_id',
        'reviewer_type',
        'overall_rating',
        'quality_rating',
        'communication_rating',
        'punctuality_rating',
        'professionalism_rating',
        'value_rating',
        'title',
        'comment',
        'photos',
        'response',
        'response_at',
        'status',
        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_reason',
        'auto_moderation_flags',
        'report_count',
        'is_published',
        'published_at',
        'is_verified',
        'is_featured',
        'helpful_count',
        'not_helpful_count',
    ];

    protected function casts(): array
    {
        return [
            'photos' => 'array',
            'response_at' => 'datetime',
            'moderated_at' => 'datetime',
            'auto_moderation_flags' => 'array',
            'published_at' => 'datetime',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Review $review) {
            if (empty($review->uuid)) {
                $review->uuid = Str::uuid();
            }
        });

        static::saved(function (Review $review) {
            if ($review->wasChanged(['overall_rating', 'status']) && $review->isApproved()) {
                $review->updateUserRating();
            }
        });

        static::deleted(function (Review $review) {
            if ($review->isApproved()) {
                $review->updateUserRating();
            }
        });
    }

    // Relations
    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_id');
    }

    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(ReviewReaction::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ReviewReport::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('reviewed_id', $userId);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('reviewer_id', $userId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByRating($query, int $rating)
    {
        return $query->where('overall_rating', $rating);
    }

    public function scopeMinRating($query, int $minRating)
    {
        return $query->where('overall_rating', '>=', $minRating);
    }

    public function scopePendingModeration($query)
    {
        return $query->where('moderation_status', 'pending');
    }

    public function scopeFlagged($query)
    {
        return $query->where('moderation_status', 'flagged');
    }

    public function scopeModerated($query)
    {
        return $query->whereIn('moderation_status', ['approved', 'rejected']);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeReported($query)
    {
        return $query->where('report_count', '>', 0);
    }

    // Status checks
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isReported(): bool
    {
        return $this->status === 'reported';
    }

    public function isPendingModeration(): bool
    {
        return $this->moderation_status === 'pending';
    }

    public function isModerationApproved(): bool
    {
        return $this->moderation_status === 'approved';
    }

    public function isModerationRejected(): bool
    {
        return $this->moderation_status === 'rejected';
    }

    public function isFlagged(): bool
    {
        return $this->moderation_status === 'flagged';
    }

    public function isPublished(): bool
    {
        return $this->is_published;
    }

    public function hasReports(): bool
    {
        return $this->report_count > 0;
    }

    public function hasResponse(): bool
    {
        return ! empty($this->response);
    }

    public function canRespond(): bool
    {
        return $this->isApproved() && ! $this->hasResponse();
    }

    // Rating helpers
    public function getDetailedRatings(): array
    {
        return [
            'quality' => $this->quality_rating,
            'communication' => $this->communication_rating,
            'punctuality' => $this->punctuality_rating,
            'professionalism' => $this->professionalism_rating,
            'value' => $this->value_rating,
        ];
    }

    public function getAverageDetailedRating(): float
    {
        $ratings = array_filter($this->getDetailedRatings());

        return empty($ratings) ? 0 : round(array_sum($ratings) / count($ratings), 1);
    }

    public function getStarsArray(): array
    {
        return [
            'filled' => $this->overall_rating,
            'empty' => 5 - $this->overall_rating,
        ];
    }

    // Actions
    public function approve(?User $moderator = null): void
    {
        $this->update([
            'status' => 'approved',
            'moderation_status' => 'approved',
            'moderated_by' => $moderator?->id,
            'moderated_at' => now(),
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    public function reject(string $reason, ?User $moderator = null): void
    {
        $this->update([
            'status' => 'rejected',
            'moderation_status' => 'rejected',
            'moderated_by' => $moderator?->id,
            'moderated_at' => now(),
            'moderation_reason' => $reason,
            'is_published' => false,
        ]);
    }

    public function flag(array $flags = [], ?User $moderator = null): void
    {
        $this->update([
            'moderation_status' => 'flagged',
            'auto_moderation_flags' => $flags,
            'moderated_by' => $moderator?->id,
            'moderated_at' => now(),
            'is_published' => false,
        ]);
    }

    public function reportBy(User $reporter, string $reason, ?string $description = null): ReviewReport
    {
        // Créer ou mettre à jour le signalement
        $report = ReviewReport::updateOrCreate(
            [
                'review_id' => $this->id,
                'reported_by' => $reporter->id,
            ],
            [
                'reason' => $reason,
                'description' => $description,
            ]
        );

        // Mettre à jour le compteur de signalements
        $this->update([
            'report_count' => $this->reports()->count(),
        ]);

        // Si plus de 3 signalements, marquer comme signalé automatiquement
        if ($this->report_count >= 3 && $this->isModerationApproved()) {
            $this->flag(['auto_flagged' => 'multiple_reports']);
        }

        return $report;
    }

    public function addResponse(string $response, User $responder): void
    {
        if ($responder->id !== $this->reviewed_id) {
            throw new \InvalidArgumentException('Seule la personne évaluée peut répondre');
        }

        $this->update([
            'response' => $response,
            'response_at' => now(),
        ]);
    }

    public function markAsHelpful(User $user): void
    {
        $this->reactions()->updateOrCreate(
            ['user_id' => $user->id],
            ['type' => 'helpful']
        );

        $this->updateReactionCounts();
    }

    public function markAsNotHelpful(User $user): void
    {
        $this->reactions()->updateOrCreate(
            ['user_id' => $user->id],
            ['type' => 'not_helpful']
        );

        $this->updateReactionCounts();
    }

    public function report(User $user, string $reason): void
    {
        $this->reactions()->updateOrCreate(
            ['user_id' => $user->id],
            ['type' => 'report', 'reason' => $reason]
        );

        // Si plus de 3 signalements, marquer comme signalé
        $reportCount = $this->reactions()->where('type', 'report')->count();
        if ($reportCount >= 3 && $this->isApproved()) {
            $this->update(['status' => 'reported']);
        }
    }

    private function updateReactionCounts(): void
    {
        $this->update([
            'helpful_count' => $this->reactions()->where('type', 'helpful')->count(),
            'not_helpful_count' => $this->reactions()->where('type', 'not_helpful')->count(),
        ]);
    }

    private function updateUserRating(): void
    {
        $user = $this->reviewed;

        if (! $user || ! $user->profile) {
            return;
        }

        $avgRating = static::approved()
            ->forUser($user->id)
            ->avg('overall_rating');

        $reviewsCount = static::approved()
            ->forUser($user->id)
            ->count();

        $user->profile->update([
            'rating' => $avgRating ? round($avgRating, 1) : 0,
            'reviews_count' => $reviewsCount,
        ]);
    }

    // Formatters
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d/m/Y');
    }

    public function getFormattedDateTimeAttribute(): string
    {
        return $this->created_at->format('d/m/Y à H:i');
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
