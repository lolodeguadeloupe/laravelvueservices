<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_id',
        'earned_at',
        'awarded_by',
        'reason',
        'context',
        'is_featured',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'earned_at' => 'datetime',
            'context' => 'array',
            'is_featured' => 'boolean',
            'is_public' => 'boolean',
        ];
    }

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }

    public function awardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('earned_at', 'desc');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper methods
    public function isRecentlyEarned(): bool
    {
        return $this->earned_at->isAfter(now()->subDays(7));
    }

    public function wasAwardedManually(): bool
    {
        return $this->awarded_by !== null;
    }

    public function toggleFeatured(): void
    {
        $this->update(['is_featured' => ! $this->is_featured]);
    }

    public function togglePublic(): void
    {
        $this->update(['is_public' => ! $this->is_public]);
    }
}
