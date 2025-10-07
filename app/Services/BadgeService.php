<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BadgeService
{
    /**
     * Vérifier et attribuer automatiquement les badges éligibles pour un utilisateur
     */
    public function checkAndAwardBadges(User $user): Collection
    {
        $earnedBadges = new Collection;

        $eligibleBadges = Badge::active()
            ->automatic()
            ->whereNotIn('id', function ($query) use ($user) {
                $query->select('badge_id')
                    ->from('user_badges')
                    ->where('user_id', $user->id);
            })
            ->get();

        foreach ($eligibleBadges as $badge) {
            if ($badge->isEligible($user)) {
                try {
                    $userBadge = $this->awardBadge($user, $badge);
                    $earnedBadges->push($userBadge);

                    Log::info("Badge '{$badge->name}' attribué à l'utilisateur {$user->id}");
                } catch (\Exception $e) {
                    Log::error("Erreur lors de l'attribution du badge '{$badge->name}' à l'utilisateur {$user->id}: ".$e->getMessage());
                }
            }
        }

        return $earnedBadges;
    }

    /**
     * Attribuer un badge à un utilisateur
     */
    public function awardBadge(User $user, Badge $badge, ?User $awardedBy = null, ?string $reason = null, ?array $context = null): UserBadge
    {
        // Vérifier si l'utilisateur a déjà ce badge
        $existingBadge = UserBadge::where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->first();

        if ($existingBadge) {
            throw new \Exception("L'utilisateur possède déjà ce badge");
        }

        return DB::transaction(function () use ($user, $badge, $awardedBy, $reason, $context) {
            $userBadge = UserBadge::create([
                'user_id' => $user->id,
                'badge_id' => $badge->id,
                'earned_at' => now(),
                'awarded_by' => $awardedBy?->id,
                'reason' => $reason,
                'context' => $context,
            ]);

            // Ajouter les points de réputation au profil utilisateur si nécessaire
            if ($badge->points > 0) {
                // S'assurer que le profil est chargé
                $user->load('profile');

                if ($user->profile) {
                    $currentPoints = $user->profile->reputation_points ?? 0;
                    $user->profile->update([
                        'reputation_points' => $currentPoints + $badge->points,
                    ]);
                }
            }

            return $userBadge;
        });
    }

    /**
     * Retirer un badge d'un utilisateur
     */
    public function revokeBadge(User $user, Badge $badge): bool
    {
        $userBadge = UserBadge::where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->first();

        if (! $userBadge) {
            return false;
        }

        return DB::transaction(function () use ($user, $badge, $userBadge) {
            // Retirer les points de réputation
            if ($badge->points > 0 && $user->profile) {
                $currentPoints = $user->profile->reputation_points ?? 0;
                $user->profile->update([
                    'reputation_points' => max(0, $currentPoints - $badge->points),
                ]);
            }

            return $userBadge->delete();
        });
    }

    /**
     * Vérifier les badges pour tous les utilisateurs (commande artisan)
     */
    public function checkAllUsers(): array
    {
        $stats = [
            'checked' => 0,
            'badges_awarded' => 0,
            'users_with_new_badges' => 0,
        ];

        User::providers()->active()->chunk(100, function ($users) use (&$stats) {
            foreach ($users as $user) {
                $stats['checked']++;
                $earnedBadges = $this->checkAndAwardBadges($user);

                if ($earnedBadges->isNotEmpty()) {
                    $stats['badges_awarded'] += $earnedBadges->count();
                    $stats['users_with_new_badges']++;
                }
            }
        });

        return $stats;
    }

    /**
     * Obtenir les badges d'un utilisateur avec pagination
     */
    public function getUserBadges(User $user, bool $publicOnly = true, bool $featuredFirst = true)
    {
        $query = UserBadge::with('badge')
            ->where('user_id', $user->id);

        if ($publicOnly) {
            $query->public();
        }

        if ($featuredFirst) {
            $query->orderBy('is_featured', 'desc');
        }

        return $query->recent()->get();
    }

    /**
     * Obtenir les statistiques des badges d'un utilisateur
     */
    public function getUserBadgeStats(User $user): array
    {
        $badges = UserBadge::with('badge')
            ->where('user_id', $user->id)
            ->get();

        $stats = [
            'total' => $badges->count(),
            'by_type' => [],
            'by_rarity' => [],
            'total_points' => 0,
            'recent_count' => 0,
            'featured_count' => 0,
        ];

        foreach ($badges as $userBadge) {
            $badge = $userBadge->badge;

            // Par type
            $type = $badge->type;
            $stats['by_type'][$type] = ($stats['by_type'][$type] ?? 0) + 1;

            // Par rareté
            $rarity = $badge->rarity;
            $stats['by_rarity'][$rarity] = ($stats['by_rarity'][$rarity] ?? 0) + 1;

            // Points totaux
            $stats['total_points'] += $badge->points;

            // Récents (7 derniers jours)
            if ($userBadge->isRecentlyEarned()) {
                $stats['recent_count']++;
            }

            // Mis en avant
            if ($userBadge->is_featured) {
                $stats['featured_count']++;
            }
        }

        return $stats;
    }

    /**
     * Obtenir le classement des utilisateurs par badges
     */
    public function getLeaderboard(int $limit = 10): Collection
    {
        return User::select('users.*')
            ->selectRaw('COUNT(user_badges.id) as badge_count')
            ->selectRaw('SUM(badges.points) as total_points')
            ->leftJoin('user_badges', 'users.id', '=', 'user_badges.user_id')
            ->leftJoin('badges', 'user_badges.badge_id', '=', 'badges.id')
            ->where('users.user_type', 'provider')
            ->where('users.is_active', true)
            ->groupBy('users.id')
            ->orderBy('total_points', 'desc')
            ->orderBy('badge_count', 'desc')
            ->limit($limit)
            ->with(['profile', 'badges'])
            ->get();
    }

    /**
     * Rechercher des badges
     */
    public function searchBadges(string $query, array $filters = []): Collection
    {
        $badgeQuery = Badge::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });

        if (! empty($filters['type'])) {
            $badgeQuery->where('type', $filters['type']);
        }

        if (! empty($filters['rarity'])) {
            $badgeQuery->where('rarity', $filters['rarity']);
        }

        return $badgeQuery->ordered()->get();
    }

    /**
     * Vérifier et attribuer les badges pour tous les utilisateurs actifs
     */
    public function checkAllUsersBadges(): array
    {
        $stats = [
            'checked' => 0,
            'badges_awarded' => 0,
            'users_with_new_badges' => 0,
        ];

        User::providers()->active()->chunk(100, function ($users) use (&$stats) {
            foreach ($users as $user) {
                $stats['checked']++;

                $earnedBadges = $this->checkAndAwardBadges($user);

                if ($earnedBadges->isNotEmpty()) {
                    $stats['badges_awarded'] += $earnedBadges->count();
                    $stats['users_with_new_badges']++;
                }
            }
        });

        return $stats;
    }

    /**
     * Créer des badges par défaut du système
     */
    public function createDefaultBadges(): void
    {
        $defaultBadges = [
            [
                'name' => 'Premier Pas',
                'slug' => 'premier-pas',
                'description' => 'Félicitations ! Vous avez complété votre première prestation.',
                'icon' => '🎯',
                'color' => '#10B981',
                'type' => 'milestone',
                'rarity' => 'common',
                'criteria' => ['min_bookings_completed' => 1],
                'points' => 10,
                'display_order' => 1,
            ],
            [
                'name' => 'Professionnel Confirmé',
                'slug' => 'professionnel-confirme',
                'description' => 'Vous avez complété 10 prestations avec succès.',
                'icon' => '💼',
                'color' => '#3B82F6',
                'type' => 'milestone',
                'rarity' => 'rare',
                'criteria' => ['min_bookings_completed' => 10],
                'points' => 50,
                'display_order' => 2,
            ],
            [
                'name' => 'Expert de la Qualité',
                'slug' => 'expert-qualite',
                'description' => 'Maintenir une note moyenne de 4.8/5 avec au moins 20 avis.',
                'icon' => '⭐',
                'color' => '#F59E0B',
                'type' => 'quality',
                'rarity' => 'epic',
                'criteria' => ['min_rating' => 4.8, 'min_reviews' => 20],
                'points' => 100,
                'display_order' => 3,
            ],
            [
                'name' => 'Légende 5 Étoiles',
                'slug' => 'legende-5-etoiles',
                'description' => 'Recevoir 50 avis 5 étoiles.',
                'icon' => '🌟',
                'color' => '#8B5CF6',
                'type' => 'achievement',
                'rarity' => 'legendary',
                'criteria' => ['min_5_star_reviews' => 50],
                'points' => 200,
                'display_order' => 4,
            ],
            [
                'name' => 'Ponctualité Parfaite',
                'slug' => 'ponctualite-parfaite',
                'description' => 'Toutes vos notes de ponctualité sont excellentes (4+ étoiles).',
                'icon' => '⏰',
                'color' => '#06B6D4',
                'type' => 'quality',
                'rarity' => 'rare',
                'criteria' => ['perfect_punctuality' => true],
                'points' => 75,
                'display_order' => 5,
            ],
            [
                'name' => 'Réactivité Exemplaire',
                'slug' => 'reactivite-exemplaire',
                'description' => 'Taux de réponse de 95% ou plus aux demandes de réservation.',
                'icon' => '⚡',
                'color' => '#EF4444',
                'type' => 'quality',
                'rarity' => 'rare',
                'criteria' => ['min_response_rate' => 95],
                'points' => 60,
                'display_order' => 6,
            ],
            [
                'name' => 'Vétéran',
                'slug' => 'veteran',
                'description' => 'Membre actif depuis plus d\'un an.',
                'icon' => '🏆',
                'color' => '#92400E',
                'type' => 'milestone',
                'rarity' => 'epic',
                'criteria' => ['min_days_active' => 365],
                'points' => 150,
                'display_order' => 7,
            ],
            [
                'name' => 'Zéro Annulation',
                'slug' => 'zero-annulation',
                'description' => 'Aucune annulation de votre part depuis le début.',
                'icon' => '✅',
                'color' => '#059669',
                'type' => 'quality',
                'rarity' => 'epic',
                'criteria' => ['no_cancellations' => true],
                'points' => 80,
                'display_order' => 8,
            ],
        ];

        foreach ($defaultBadges as $badgeData) {
            Badge::updateOrCreate(
                ['slug' => $badgeData['slug']],
                $badgeData
            );
        }
    }
}
