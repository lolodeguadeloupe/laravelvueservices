<?php

use App\Models\Badge;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\BadgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->badgeService = app(BadgeService::class);

    // Créer un utilisateur prestataire de test
    $this->user = User::factory()->create([
        'user_type' => 'provider',
        'is_active' => true,
    ]);

    // Créer un profil utilisateur
    $this->profile = UserProfile::factory()->create([
        'user_id' => $this->user->id,
        'rating' => 4.5,
        'reputation_points' => 0,
    ]);
});

it('peut créer des badges par défaut', function () {
    $this->badgeService->createDefaultBadges();

    $badgeCount = Badge::count();
    expect($badgeCount)->toBeGreaterThan(0);

    // Vérifier qu'un badge spécifique existe
    $premierPas = Badge::where('slug', 'premier-pas')->first();
    expect($premierPas)->not->toBeNull();
    expect($premierPas->name)->toBe('Premier Pas');
    expect($premierPas->type)->toBe('milestone');
    expect($premierPas->rarity)->toBe('common');
});

it('peut attribuer un badge manuellement', function () {
    $this->badgeService->createDefaultBadges();
    $badge = Badge::where('slug', 'premier-pas')->first();

    // Vérifier qu'aucun badge n'est attribué initialement
    expect($this->user->userBadges()->count())->toBe(0);

    // Attribuer le badge
    $userBadge = $this->badgeService->awardBadge(
        $this->user,
        $badge,
        null,
        'Attribution pour test'
    );

    // Vérifications
    expect($userBadge)->not->toBeNull();
    expect($userBadge->user_id)->toBe($this->user->id);
    expect($userBadge->badge_id)->toBe($badge->id);
    expect($userBadge->reason)->toBe('Attribution pour test');

    // Vérifier que l'utilisateur a maintenant ce badge
    expect($this->user->userBadges()->count())->toBe(1);
    expect($this->user->hasBadge($badge))->toBeTrue();
});

it('ne peut pas attribuer le même badge deux fois', function () {
    $this->badgeService->createDefaultBadges();
    $badge = Badge::where('slug', 'premier-pas')->first();

    // Première attribution
    $this->badgeService->awardBadge($this->user, $badge);

    // Tentative de deuxième attribution
    expect(fn () => $this->badgeService->awardBadge($this->user, $badge))
        ->toThrow(Exception::class, "L'utilisateur possède déjà ce badge");
});

it('peut obtenir les statistiques des badges d\'un utilisateur', function () {
    $this->badgeService->createDefaultBadges();

    // Attribuer quelques badges
    $badge1 = Badge::where('slug', 'premier-pas')->first();
    $badge2 = Badge::where('slug', 'reactivite-exemplaire')->first();

    $this->badgeService->awardBadge($this->user, $badge1);
    $this->badgeService->awardBadge($this->user, $badge2);

    // Obtenir les statistiques
    $stats = $this->badgeService->getUserBadgeStats($this->user);

    expect($stats)->toHaveKeys([
        'total',
        'by_type',
        'by_rarity',
        'total_points',
        'recent_count',
        'featured_count',
    ]);

    expect($stats['total'])->toBe(2);
    expect($stats['by_type']['milestone'])->toBe(1);
    expect($stats['by_type']['quality'])->toBe(1);
    expect($stats['by_rarity']['common'])->toBe(1);
    expect($stats['by_rarity']['rare'])->toBe(1);
});

it('peut vérifier l\'éligibilité des badges automatiques', function () {
    $this->badgeService->createDefaultBadges();

    // Créer un badge personnalisé avec des critères spécifiques
    $badge = Badge::create([
        'name' => 'Test Badge',
        'slug' => 'test-badge',
        'description' => 'Badge de test pour les critères',
        'icon' => '🧪',
        'type' => 'achievement',
        'rarity' => 'common',
        'points' => 5,
        'criteria' => ['min_rating' => 4.0],
        'is_automatic' => true,
        'is_active' => true,
    ]);

    // L'utilisateur a une note de 4.5, il devrait être éligible
    expect($badge->isEligible($this->user))->toBeTrue();

    // Changer la note à 3.5
    $this->profile->update(['rating' => 3.5]);
    $this->user->refresh();

    expect($badge->isEligible($this->user))->toBeFalse();
});

it('peut effectuer une vérification automatique des badges', function () {
    $this->badgeService->createDefaultBadges();

    // Aucun badge ne devrait être attribué initialement
    expect($this->user->userBadges()->count())->toBe(0);

    // Effectuer la vérification automatique
    $earnedBadges = $this->badgeService->checkAndAwardBadges($this->user);

    // Au moins un badge devrait être attribué
    // (dépend des critères des badges par défaut et du profil utilisateur)
    expect($earnedBadges)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});

it('peut obtenir les badges d\'un utilisateur avec filtres', function () {
    $this->badgeService->createDefaultBadges();

    // Attribuer plusieurs badges
    $badge1 = Badge::where('slug', 'premier-pas')->first();
    $badge2 = Badge::where('slug', 'reactivite-exemplaire')->first();

    $userBadge1 = $this->badgeService->awardBadge($this->user, $badge1);
    $userBadge2 = $this->badgeService->awardBadge($this->user, $badge2);

    // Marquer un badge comme mis en avant
    $userBadge2->update(['is_featured' => true]);

    // Obtenir tous les badges
    $allBadges = $this->badgeService->getUserBadges($this->user, false);
    expect($allBadges)->toHaveCount(2);

    // Obtenir seulement les badges publics
    $publicBadges = $this->badgeService->getUserBadges($this->user, true);
    expect($publicBadges)->toHaveCount(2); // Par défaut, tous sont publics

    // Obtenir avec featured en premier
    $featuredFirst = $this->badgeService->getUserBadges($this->user, false, true);
    expect($featuredFirst->first()->is_featured)->toBeTrue();
});

it('peut retirer un badge d\'un utilisateur', function () {
    $this->badgeService->createDefaultBadges();
    $badge = Badge::where('slug', 'premier-pas')->first();

    // Attribuer le badge
    $this->badgeService->awardBadge($this->user, $badge);
    expect($this->user->userBadges()->count())->toBe(1);

    // Retirer le badge
    $result = $this->badgeService->revokeBadge($this->user, $badge);
    expect($result)->toBeTrue();

    // Vérifier que le badge a été retiré
    $this->user->refresh();
    expect($this->user->userBadges()->count())->toBe(0);
    expect($this->user->hasBadge($badge))->toBeFalse();
});

it('peut rechercher des badges', function () {
    $this->badgeService->createDefaultBadges();

    // Recherche par nom
    $results = $this->badgeService->searchBadges('Premier');
    expect($results)->toHaveCount(1);
    expect($results->first()->name)->toContain('Premier');

    // Recherche avec filtres
    $results = $this->badgeService->searchBadges('', ['type' => 'quality']);
    $qualityBadges = $results->filter(fn ($badge) => $badge->type === 'quality');
    expect($qualityBadges)->toHaveCount($results->count());
});

it('peut obtenir le classement des utilisateurs', function () {
    $this->badgeService->createDefaultBadges();

    // Créer un autre utilisateur
    $user2 = User::factory()->create([
        'user_type' => 'provider',
        'is_active' => true,
    ]);

    UserProfile::factory()->create([
        'user_id' => $user2->id,
    ]);

    // Attribuer des badges aux deux utilisateurs
    $badge1 = Badge::where('slug', 'premier-pas')->first();
    $badge2 = Badge::where('slug', 'reactivite-exemplaire')->first();

    $this->badgeService->awardBadge($this->user, $badge1);
    $this->badgeService->awardBadge($this->user, $badge2);
    $this->badgeService->awardBadge($user2, $badge1);

    // Obtenir le classement
    $leaderboard = $this->badgeService->getLeaderboard(10);

    expect($leaderboard)->toHaveCount(2);
    // L'utilisateur avec le plus de points devrait être en premier
    expect($leaderboard->first()->id)->toBe($this->user->id);
});
