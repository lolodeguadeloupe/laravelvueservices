<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct(
        private readonly BadgeService $badgeService
    ) {}

    /**
     * Display the user's profile.
     */
    public function show(User $user): Response
    {
        // Charger les badges de l'utilisateur
        $userBadges = $user->userBadges()
            ->with('badge')
            ->public()
            ->latest('earned_at')
            ->get();

        // Statistiques des badges
        $badgeStats = $this->badgeService->getUserBadgeStats($user);

        return Inertia::render('Profile/Show', [
            'user' => $user->load('profile'),
            'badges' => $userBadges,
            'badgeStats' => $badgeStats,
            'isOwner' => auth()->id() === $user->id,
        ]);
    }

    /**
     * Display the authenticated user's profile.
     */
    public function index(): Response
    {
        $user = auth()->user();

        // Charger tous les badges (publics et privés) pour le propriétaire
        $userBadges = $user->userBadges()
            ->with('badge')
            ->latest('earned_at')
            ->get();

        // Statistiques des badges
        $badgeStats = $this->badgeService->getUserBadgeStats($user);

        return Inertia::render('Profile/Index', [
            'user' => $user->load('profile'),
            'badges' => $userBadges,
            'badgeStats' => $badgeStats,
        ]);
    }

    /**
     * Get user badges for API.
     */
    public function badges(Request $request, ?User $user = null): array
    {
        $user = $user ?? auth()->user();
        $publicOnly = ! auth()->check() || auth()->id() !== $user->id;

        $userBadges = $this->badgeService->getUserBadges($user, $publicOnly);
        $badgeStats = $this->badgeService->getUserBadgeStats($user);

        return [
            'badges' => $userBadges,
            'stats' => $badgeStats,
        ];
    }

    /**
     * Toggle badge visibility (public/private).
     */
    public function toggleBadgeVisibility(Request $request): array
    {
        $request->validate([
            'badge_id' => 'required|exists:badges,id',
            'is_public' => 'required|boolean',
        ]);

        $user = auth()->user();

        $userBadge = $user->userBadges()
            ->where('badge_id', $request->badge_id)
            ->firstOrFail();

        $userBadge->update(['is_public' => $request->is_public]);

        return [
            'message' => $request->is_public
                ? 'Badge rendu public'
                : 'Badge rendu privé',
            'badge' => $userBadge->fresh()->load('badge'),
        ];
    }

    /**
     * Toggle badge featured status.
     */
    public function toggleBadgeFeatured(Request $request): array
    {
        $request->validate([
            'badge_id' => 'required|exists:badges,id',
            'is_featured' => 'required|boolean',
        ]);

        $user = auth()->user();

        // Vérifier le nombre de badges mis en avant (max 3)
        if ($request->is_featured) {
            $featuredCount = $user->userBadges()->where('is_featured', true)->count();
            if ($featuredCount >= 3) {
                return [
                    'error' => 'Vous ne pouvez mettre en avant que 3 badges maximum.',
                ];
            }
        }

        $userBadge = $user->userBadges()
            ->where('badge_id', $request->badge_id)
            ->firstOrFail();

        $userBadge->update(['is_featured' => $request->is_featured]);

        return [
            'message' => $request->is_featured
                ? 'Badge mis en avant'
                : 'Badge retiré de la mise en avant',
            'badge' => $userBadge->fresh()->load('badge'),
        ];
    }
}
