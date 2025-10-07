<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private readonly BadgeService $badgeService
    ) {}

    /**
     * Liste des utilisateurs
     */
    public function index(Request $request): Response
    {
        $query = User::with(['profile', 'userBadges.badge'])
            ->withCount(['services', 'clientBookings', 'providerBookings', 'userBadges']);

        // Filtres
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['user_type', 'is_active', 'search', 'sort_by', 'sort_order']),
            'stats' => [
                'total' => User::count(),
                'clients' => User::where('user_type', 'client')->count(),
                'providers' => User::where('user_type', 'provider')->count(),
                'admins' => User::where('user_type', 'admin')->count(),
                'active' => User::where('is_active', true)->count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
            ],
        ]);
    }

    /**
     * Voir les détails d'un utilisateur
     */
    public function show(User $user): Response
    {
        $user->load([
            'profile',
            'userBadges.badge',
            'services.category',
            'clientBookings.service',
            'providerBookings.service',
            'reviewsGiven.reviewed',
            'reviewsReceived.reviewer',
        ]);

        $badgeStats = $this->badgeService->getUserBadgeStats($user);

        // Statistiques spécifiques à l'utilisateur
        $userStats = [
            'total_services' => $user->services->count(),
            'active_services' => $user->services->where('is_active', true)->count(),
            'total_bookings_as_client' => $user->clientBookings->count(),
            'total_bookings_as_provider' => $user->providerBookings->count(),
            'completed_bookings' => $user->providerBookings->where('status', 'completed')->count(),
            'total_reviews_given' => $user->reviewsGiven->count(),
            'total_reviews_received' => $user->reviewsReceived->count(),
            'average_rating_received' => $user->profile?->rating ?? 0,
        ];

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'badgeStats' => $badgeStats,
            'userStats' => $userStats,
        ]);
    }

    /**
     * Mettre à jour le statut d'un utilisateur
     */
    public function updateStatus(Request $request, User $user): array
    {
        $request->validate([
            'is_active' => 'required|boolean',
            'reason' => 'nullable|string|max:500',
        ]);

        $user->update([
            'is_active' => $request->is_active,
        ]);

        // Log de l'action admin
        activity()
            ->performedOn($user)
            ->by(auth()->user())
            ->withProperties([
                'action' => $request->is_active ? 'activated' : 'deactivated',
                'reason' => $request->reason,
            ])
            ->log($request->is_active ? 'Utilisateur activé' : 'Utilisateur désactivé');

        return [
            'message' => $request->is_active
                ? 'Utilisateur activé avec succès'
                : 'Utilisateur désactivé avec succès',
            'user' => $user->fresh(),
        ];
    }

    /**
     * Changer le type d'utilisateur
     */
    public function updateUserType(Request $request, User $user): array
    {
        $request->validate([
            'user_type' => 'required|in:client,provider,admin',
            'reason' => 'nullable|string|max:500',
        ]);

        $oldType = $user->user_type;

        $user->update([
            'user_type' => $request->user_type,
        ]);

        // Log de l'action admin
        activity()
            ->performedOn($user)
            ->by(auth()->user())
            ->withProperties([
                'old_type' => $oldType,
                'new_type' => $request->user_type,
                'reason' => $request->reason,
            ])
            ->log("Type d'utilisateur modifié de {$oldType} vers {$request->user_type}");

        return [
            'message' => 'Type d\'utilisateur modifié avec succès',
            'user' => $user->fresh(),
        ];
    }

    /**
     * Vérifier manuellement un utilisateur
     */
    public function verify(Request $request, User $user): array
    {
        if ($user->email_verified_at) {
            return [
                'error' => 'Cet utilisateur est déjà vérifié',
            ];
        }

        $user->update([
            'email_verified_at' => now(),
        ]);

        // Log de l'action admin
        activity()
            ->performedOn($user)
            ->by(auth()->user())
            ->withProperties([
                'reason' => $request->reason,
            ])
            ->log('Utilisateur vérifié manuellement');

        return [
            'message' => 'Utilisateur vérifié avec succès',
            'user' => $user->fresh(),
        ];
    }

    /**
     * Attribuer un badge à un utilisateur
     */
    public function awardBadge(Request $request, User $user): array
    {
        $request->validate([
            'badge_id' => 'required|exists:badges,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $badge = \App\Models\Badge::findOrFail($request->badge_id);

        try {
            $userBadge = $this->badgeService->awardBadge(
                $user,
                $badge,
                auth()->user(),
                $request->reason
            );

            return [
                'message' => 'Badge attribué avec succès',
                'userBadge' => $userBadge->load('badge'),
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retirer un badge d'un utilisateur
     */
    public function revokeBadge(Request $request, User $user): array
    {
        $request->validate([
            'badge_id' => 'required|exists:badges,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $badge = \App\Models\Badge::findOrFail($request->badge_id);

        $success = $this->badgeService->revokeBadge($user, $badge);

        if ($success) {
            // Log de l'action admin
            activity()
                ->performedOn($user)
                ->by(auth()->user())
                ->withProperties([
                    'badge_name' => $badge->name,
                    'reason' => $request->reason,
                ])
                ->log("Badge '{$badge->name}' retiré");

            return [
                'message' => 'Badge retiré avec succès',
            ];
        }

        return [
            'error' => 'Impossible de retirer ce badge (utilisateur ne le possède pas)',
        ];
    }

    /**
     * Supprimer un utilisateur (soft delete)
     */
    public function destroy(Request $request, User $user): array
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Vérifier que ce n'est pas un admin qui se supprime lui-même
        if ($user->id === auth()->id()) {
            return [
                'error' => 'Vous ne pouvez pas supprimer votre propre compte',
            ];
        }

        // Log de l'action admin avant suppression
        activity()
            ->performedOn($user)
            ->by(auth()->user())
            ->withProperties([
                'reason' => $request->reason,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ])
            ->log('Utilisateur supprimé');

        $user->delete();

        return [
            'message' => 'Utilisateur supprimé avec succès',
        ];
    }

    /**
     * Exporter les données utilisateurs
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $query = User::with('profile');

        // Filtres pour l'export
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->get();

        // Créer le CSV
        $filename = 'users_export_'.now()->format('Y-m-d_H-i-s').'.csv';
        $handle = fopen(storage_path('app/'.$filename), 'w');

        // En-têtes CSV
        fputcsv($handle, [
            'ID',
            'Nom',
            'Email',
            'Type',
            'Actif',
            'Vérifié',
            'Date d\'inscription',
            'Dernière connexion',
            'Note moyenne',
            'Points de réputation',
        ]);

        // Données
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->name,
                $user->email,
                $user->user_type,
                $user->is_active ? 'Oui' : 'Non',
                $user->email_verified_at ? 'Oui' : 'Non',
                $user->created_at->format('Y-m-d H:i:s'),
                $user->last_login_at?->format('Y-m-d H:i:s') ?? 'Jamais',
                $user->profile?->rating ?? 'N/A',
                $user->profile?->reputation_points ?? 0,
            ]);
        }

        fclose($handle);

        return response()->download(storage_path('app/'.$filename))->deleteFileAfterSend();
    }
}
