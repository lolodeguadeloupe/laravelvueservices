<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'type',
        'rarity',
        'criteria',
        'points',
        'is_active',
        'is_automatic',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'criteria' => 'array',
            'is_active' => 'boolean',
            'is_automatic' => 'boolean',
            'points' => 'integer',
            'display_order' => 'integer',
        ];
    }

    // Relations
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot(['earned_at', 'awarded_by', 'reason', 'context', 'is_featured', 'is_public'])
            ->withTimestamps();
    }

    public function userBadges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAutomatic($query)
    {
        return $query->where('is_automatic', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByRarity($query, string $rarity)
    {
        return $query->where('rarity', $rarity);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    // Helper methods
    public function isEligible(User $user): bool
    {
        if (! $this->is_active || ! $this->is_automatic) {
            return false;
        }

        // Vérifier si l'utilisateur a déjà ce badge
        if ($this->users()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return $this->checkCriteria($user);
    }

    protected function checkCriteria(User $user): bool
    {
        if (empty($this->criteria)) {
            return false;
        }

        foreach ($this->criteria as $criterion => $value) {
            if (! $this->evaluateCriterion($user, $criterion, $value)) {
                return false;
            }
        }

        return true;
    }

    protected function evaluateCriterion(User $user, string $criterion, $value): bool
    {
        switch ($criterion) {
            case 'min_bookings_completed':
                return $user->providerBookings()->where('status', 'completed')->count() >= $value;

            case 'min_rating':
                $rating = $user->profile?->rating ?? 0;

                return $rating >= $value;

            case 'min_reviews':
                return $user->reviewsReceived()->where('status', 'approved')->count() >= $value;

            case 'min_5_star_reviews':
                return $user->reviewsReceived()
                    ->where('status', 'approved')
                    ->where('overall_rating', 5)
                    ->count() >= $value;

            case 'min_response_rate':
                // Calculer le taux de réponse aux demandes
                $totalRequests = $user->providerBookings()->count();
                if ($totalRequests === 0) {
                    return false;
                }

                $responseRate = $user->providerBookings()
                    ->whereIn('status', ['accepted', 'rejected'])
                    ->count() / $totalRequests * 100;

                return $responseRate >= $value;

            case 'min_days_active':
                return $user->created_at->diffInDays(now()) >= $value;

            case 'category_specialist':
                // Vérifier si le prestataire a au moins X services dans une catégorie
                return $user->providedServices()
                    ->where('category_id', $value['category_id'])
                    ->count() >= ($value['min_services'] ?? 1);

            case 'no_cancellations':
                return $user->providerBookings()->where('status', 'cancelled')->count() === 0;

            case 'perfect_punctuality':
                // Vérifier les notes de ponctualité (toutes >= 4)
                $reviews = $user->reviewsReceived()
                    ->where('status', 'approved')
                    ->whereNotNull('punctuality_rating')
                    ->get();

                if ($reviews->isEmpty()) {
                    return false;
                }

                return $reviews->every(function ($review) {
                    return $review->punctuality_rating >= 4;
                });

            case 'streak_days':
                // Vérifier une série de jours consécutifs avec activité
                // Cette logique pourrait être plus complexe selon les besoins
                return $user->last_active_at &&
                       $user->last_active_at->diffInDays(now()) <= ($value ?? 7);

            default:
                return false;
        }
    }

    public function awardTo(User $user, ?User $awardedBy = null, ?string $reason = null, ?array $context = null): UserBadge
    {
        return UserBadge::create([
            'user_id' => $user->id,
            'badge_id' => $this->id,
            'earned_at' => now(),
            'awarded_by' => $awardedBy?->id,
            'reason' => $reason,
            'context' => $context,
        ]);
    }

    public function getRarityColorAttribute(): string
    {
        return match ($this->rarity) {
            'common' => '#6B7280',    // Gray
            'rare' => '#3B82F6',      // Blue
            'epic' => '#8B5CF6',      // Purple
            'legendary' => '#F59E0B', // Amber
            default => '#6B7280',
        };
    }

    public function getRarityLabelAttribute(): string
    {
        return match ($this->rarity) {
            'common' => 'Commun',
            'rare' => 'Rare',
            'epic' => 'Épique',
            'legendary' => 'Légendaire',
            default => 'Commun',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'achievement' => 'Accomplissement',
            'certification' => 'Certification',
            'milestone' => 'Étape importante',
            'quality' => 'Qualité',
            default => 'Badge',
        };
    }
}
