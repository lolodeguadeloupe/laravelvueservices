<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBadgeRequest;
use App\Http\Requests\UpdateBadgeRequest;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Services\BadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BadgeController extends Controller
{
    public function __construct(
        private readonly BadgeService $badgeService
    ) {}

    /**
     * Display the badge management interface.
     */
    public function index(): Response
    {
        $badges = Badge::withCount(['userBadges as awarded_count'])
            ->orderBy('type')
            ->orderByRaw("FIELD(rarity, 'legendary', 'epic', 'rare', 'common')")
            ->get();

        $stats = [
            'total_badges' => Badge::count(),
            'awarded_badges' => UserBadge::count(),
            'active_badges' => Badge::where('is_active', true)->count(),
            'total_points' => Badge::sum('points'),
        ];

        return Inertia::render('Admin/Badges/Index', [
            'badges' => $badges,
            'stats' => $stats,
        ]);
    }

    /**
     * Get badges data for API.
     */
    public function list(Request $request): JsonResponse
    {
        $query = Badge::withCount(['userBadges as awarded_count']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->filled('rarity')) {
            $query->where('rarity', $request->get('rarity'));
        }

        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        $badges = $query->orderBy('type')
            ->orderByRaw("FIELD(rarity, 'legendary', 'epic', 'rare', 'common')")
            ->get();

        return response()->json($badges);
    }

    /**
     * Store a newly created badge.
     */
    public function store(StoreBadgeRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Préparer les critères pour l'attribution automatique
        if ($data['is_automatic'] && isset($data['criteria'])) {
            $criteria = collect($data['criteria'])
                ->filter(fn ($criterion) => ! empty($criterion['field']) && ! empty($criterion['value']))
                ->mapWithKeys(fn ($criterion) => [$criterion['field'] => $criterion['value']])
                ->toArray();

            $data['criteria'] = $criteria;
        } else {
            $data['criteria'] = [];
        }

        $badge = Badge::create($data);

        return response()->json([
            'message' => 'Badge créé avec succès',
            'badge' => $badge->load('userBadges:id,badge_id'),
        ], 201);
    }

    /**
     * Display the specified badge.
     */
    public function show(Badge $badge): JsonResponse
    {
        return response()->json($badge->loadCount('userBadges'));
    }

    /**
     * Update the specified badge.
     */
    public function update(UpdateBadgeRequest $request, Badge $badge): JsonResponse
    {
        $data = $request->validated();

        // Préparer les critères pour l'attribution automatique
        if ($data['is_automatic'] && isset($data['criteria'])) {
            $criteria = collect($data['criteria'])
                ->filter(fn ($criterion) => ! empty($criterion['field']) && ! empty($criterion['value']))
                ->mapWithKeys(fn ($criterion) => [$criterion['field'] => $criterion['value']])
                ->toArray();

            $data['criteria'] = $criteria;
        } else {
            $data['criteria'] = [];
        }

        $badge->update($data);

        return response()->json([
            'message' => 'Badge modifié avec succès',
            'badge' => $badge->fresh()->loadCount('userBadges'),
        ]);
    }

    /**
     * Toggle badge status (active/inactive).
     */
    public function toggleStatus(Badge $badge): JsonResponse
    {
        $badge->update(['is_active' => ! $badge->is_active]);

        return response()->json([
            'message' => $badge->is_active ? 'Badge activé' : 'Badge désactivé',
            'badge' => $badge->fresh(),
        ]);
    }

    /**
     * Remove the specified badge.
     */
    public function destroy(Badge $badge): JsonResponse
    {
        // Vérifier si le badge a été attribué
        if ($badge->userBadges()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer un badge qui a été attribué à des utilisateurs.',
            ], 422);
        }

        $badge->delete();

        return response()->json([
            'message' => 'Badge supprimé avec succès',
        ]);
    }

    /**
     * Check and award badges for all users.
     */
    public function checkAll(): JsonResponse
    {
        $stats = $this->badgeService->checkAllUsersBadges();

        return response()->json([
            'message' => 'Vérification des badges terminée',
            'stats' => $stats,
        ]);
    }

    /**
     * Check and award badges for a specific user.
     */
    public function checkUser(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $earnedBadges = $this->badgeService->checkAndAwardBadges($user);

        return response()->json([
            'message' => $earnedBadges->isEmpty()
                ? 'Aucun nouveau badge attribué'
                : "{$earnedBadges->count()} nouveau(x) badge(s) attribué(s)",
            'earned_badges' => $earnedBadges->load('badge'),
        ]);
    }

    /**
     * Get badge statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_badges' => Badge::count(),
            'awarded_badges' => UserBadge::count(),
            'active_badges' => Badge::where('is_active', true)->count(),
            'total_points' => Badge::sum('points'),
            'badges_by_type' => Badge::groupBy('type')
                ->selectRaw('type, count(*) as count')
                ->pluck('count', 'type'),
            'badges_by_rarity' => Badge::groupBy('rarity')
                ->selectRaw('rarity, count(*) as count')
                ->pluck('count', 'rarity'),
            'recent_awards' => UserBadge::with(['badge:id,name,icon', 'user:id,name'])
                ->latest()
                ->limit(10)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Award a badge manually to a user.
     */
    public function awardBadge(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'badge_id' => 'required|exists:badges,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $badge = Badge::findOrFail($request->badge_id);

        $userBadge = $this->badgeService->awardBadge(
            $user,
            $badge,
            $request->user(),
            $request->reason ?? 'Attribution manuelle par un administrateur'
        );

        return response()->json([
            'message' => "Badge '{$badge->name}' attribué à {$user->name}",
            'user_badge' => $userBadge->load('badge'),
        ]);
    }

    /**
     * Revoke a badge from a user.
     */
    public function revokeBadge(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'badge_id' => 'required|exists:badges,id',
        ]);

        $userBadge = UserBadge::where('user_id', $request->user_id)
            ->where('badge_id', $request->badge_id)
            ->first();

        if (! $userBadge) {
            return response()->json([
                'message' => 'Ce badge n\'a pas été attribué à cet utilisateur.',
            ], 404);
        }

        $user = $userBadge->user;
        $badge = $userBadge->badge;

        // Retirer les points de réputation
        if ($user->profile) {
            $user->profile->decrement('reputation_points', $badge->points);
        }

        $userBadge->delete();

        return response()->json([
            'message' => "Badge '{$badge->name}' retiré de {$user->name}",
        ]);
    }
}
