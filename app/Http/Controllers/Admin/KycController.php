<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\KycService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class KycController extends Controller
{
    protected KycService $kycService;

    public function __construct(KycService $kycService)
    {
        $this->kycService = $kycService;
        $this->middleware('admin');
    }

    /**
     * Liste des demandes KYC en attente
     */
    public function index(): Response
    {
        $pendingRequests = User::whereHas('profile', function ($query) {
            $query->whereIn('kyc_status', ['pending', 'under_review']);
        })
            ->with(['profile'])
            ->where('user_type', 'provider')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Kyc/Index', [
            'pendingRequests' => $pendingRequests,
        ]);
    }

    /**
     * Détails d'une demande KYC
     */
    public function show(User $user): Response
    {
        $user->load('profile');

        if ($user->user_type !== 'provider') {
            abort(404);
        }

        $documents = $this->kycService->getKycDocuments($user);

        return Inertia::render('Admin/Kyc/Show', [
            'provider' => $user,
            'documents' => $documents,
            'kycStatus' => $this->kycService->getKycStatus($user),
        ]);
    }

    /**
     * Mettre à jour le statut KYC en "en cours de révision"
     */
    public function review(User $user): JsonResponse
    {
        if ($user->user_type !== 'provider') {
            return response()->json(['error' => 'Utilisateur invalide'], 400);
        }

        $user->profile->update([
            'kyc_status' => 'under_review',
            'kyc_reviewed_at' => now(),
            'kyc_reviewed_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Demande KYC mise en révision',
            'status' => 'under_review',
        ]);
    }

    /**
     * Approuver une demande KYC
     */
    public function approve(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($user->user_type !== 'provider') {
            return response()->json(['error' => 'Utilisateur invalide'], 400);
        }

        $success = $this->kycService->approveKyc($user, auth()->user());

        if ($success) {
            // Log de l'action admin
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties([
                    'action' => 'kyc_approved',
                    'notes' => $request->notes,
                ])
                ->log('KYC approuvé pour le prestataire');

            return response()->json([
                'message' => 'Demande KYC approuvée avec succès',
                'status' => 'approved',
            ]);
        }

        return response()->json(['error' => 'Erreur lors de l\'approbation'], 500);
    }

    /**
     * Rejeter une demande KYC
     */
    public function reject(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
            'missing_documents' => ['nullable', 'array'],
            'missing_documents.*' => ['string'],
        ]);

        if ($user->user_type !== 'provider') {
            return response()->json(['error' => 'Utilisateur invalide'], 400);
        }

        $rejectionReason = $request->reason;
        if (! empty($request->missing_documents)) {
            $rejectionReason .= "\n\nDocuments manquants ou incorrects:\n- ".
                               implode("\n- ", $request->missing_documents);
        }

        $success = $this->kycService->rejectKyc($user, auth()->user(), $rejectionReason);

        if ($success) {
            // Log de l'action admin
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->withProperties([
                    'action' => 'kyc_rejected',
                    'reason' => $rejectionReason,
                ])
                ->log('KYC rejeté pour le prestataire');

            return response()->json([
                'message' => 'Demande KYC rejetée',
                'status' => 'rejected',
            ]);
        }

        return response()->json(['error' => 'Erreur lors du rejet'], 500);
    }

    /**
     * Télécharger un document KYC
     */
    public function downloadDocument(User $user, string $field): mixed
    {
        if ($user->user_type !== 'provider' || ! $user->profile) {
            abort(404);
        }

        $documentPath = $user->profile->$field;

        if (! $documentPath || ! Storage::disk('private')->exists($documentPath)) {
            abort(404, 'Document non trouvé');
        }

        // Log de l'accès au document
        activity()
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties([
                'action' => 'document_accessed',
                'document_field' => $field,
            ])
            ->log('Document KYC consulté');

        return Storage::disk('private')->download($documentPath);
    }

    /**
     * Statistiques KYC pour le dashboard admin
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'pending' => User::whereHas('profile', function ($query) {
                $query->where('kyc_status', 'pending');
            })->where('user_type', 'provider')->count(),

            'under_review' => User::whereHas('profile', function ($query) {
                $query->where('kyc_status', 'under_review');
            })->where('user_type', 'provider')->count(),

            'approved' => User::whereHas('profile', function ($query) {
                $query->where('kyc_status', 'approved');
            })->where('user_type', 'provider')->count(),

            'rejected' => User::whereHas('profile', function ($query) {
                $query->where('kyc_status', 'rejected');
            })->where('user_type', 'provider')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Historique des validations KYC
     */
    public function history(): Response
    {
        $history = User::whereHas('profile', function ($query) {
            $query->whereIn('kyc_status', ['approved', 'rejected']);
        })
            ->with(['profile'])
            ->where('user_type', 'provider')
            ->orderBy('updated_at', 'desc')
            ->paginate(50);

        return Inertia::render('Admin/Kyc/History', [
            'history' => $history,
        ]);
    }

    /**
     * Exporter les données KYC (pour audit)
     */
    public function export(): JsonResponse
    {
        $providers = User::whereHas('profile', function ($query) {
            $query->whereNotNull('kyc_status');
        })
            ->with(['profile'])
            ->where('user_type', 'provider')
            ->get();

        $exportData = $providers->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'business_name' => $user->profile->business_name,
                'business_type' => $user->profile->business_type,
                'kyc_status' => $user->profile->kyc_status,
                'kyc_submitted_at' => $user->profile->kyc_submitted_at,
                'kyc_reviewed_at' => $user->profile->kyc_reviewed_at,
                'created_at' => $user->created_at,
            ];
        });

        return response()->json([
            'data' => $exportData,
            'export_date' => now(),
            'total_records' => $exportData->count(),
        ]);
    }
}
