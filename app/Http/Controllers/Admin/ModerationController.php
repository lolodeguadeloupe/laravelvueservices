<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewReport;
use App\Services\ModerationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ModerationController extends Controller
{
    public function __construct(
        private readonly ModerationService $moderationService
    ) {}

    /**
     * Dashboard de modération
     */
    public function index(): Response
    {
        $stats = $this->moderationService->getModerationStats();

        $pendingReviews = $this->moderationService->getPendingReviews()->take(5);
        $flaggedReviews = $this->moderationService->getFlaggedReviews()->take(5);
        $pendingReports = $this->moderationService->getPendingReports()->take(5);

        return Inertia::render('Admin/Moderation/Dashboard', [
            'stats' => $stats,
            'pendingReviews' => $pendingReviews,
            'flaggedReviews' => $flaggedReviews,
            'pendingReports' => $pendingReports,
        ]);
    }

    /**
     * Liste des avis en attente de modération
     */
    public function pendingReviews(): Response
    {
        $reviews = $this->moderationService->getPendingReviews();

        return Inertia::render('Admin/Moderation/PendingReviews', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * Liste des avis signalés
     */
    public function flaggedReviews(): Response
    {
        $reviews = $this->moderationService->getFlaggedReviews();

        return Inertia::render('Admin/Moderation/FlaggedReviews', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * Liste des signalements
     */
    public function reports(): Response
    {
        $reports = $this->moderationService->getPendingReports();

        return Inertia::render('Admin/Moderation/Reports', [
            'reports' => $reports,
        ]);
    }

    /**
     * Modérer un avis
     */
    public function moderateReview(Request $request, Review $review): array
    {
        $request->validate([
            'action' => 'required|in:approve,reject,flag',
            'reason' => 'required_if:action,reject,flag|string|max:500',
        ]);

        $success = $this->moderationService->moderateReview(
            $review,
            $request->action,
            auth()->user(),
            $request->reason
        );

        if ($success) {
            return [
                'message' => 'Avis modéré avec succès',
                'review' => $review->fresh(['reviewer', 'reviewed']),
            ];
        }

        return [
            'error' => 'Erreur lors de la modération',
        ];
    }

    /**
     * Modération en lot
     */
    public function bulkModerate(Request $request): array
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
            'action' => 'required|in:approve,reject,flag',
            'reason' => 'required_if:action,reject,flag|string|max:500',
        ]);

        $results = $this->moderationService->bulkModerate(
            $request->review_ids,
            $request->action,
            auth()->user(),
            $request->reason
        );

        return [
            'message' => "Modération en lot terminée: {$results['success']} succès, {$results['failed']} échecs",
            'results' => $results,
        ];
    }

    /**
     * Traiter un signalement
     */
    public function processReport(Request $request, ReviewReport $report): array
    {
        $request->validate([
            'action' => 'required|in:validate,dismiss',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $success = $this->moderationService->processReport(
            $report,
            $request->action,
            auth()->user(),
            $request->admin_notes
        );

        if ($success) {
            return [
                'message' => 'Signalement traité avec succès',
                'report' => $report->fresh(['review', 'reporter', 'reviewer']),
            ];
        }

        return [
            'error' => 'Erreur lors du traitement du signalement',
        ];
    }

    /**
     * Voir les détails d'un avis pour modération
     */
    public function showReview(Review $review): Response
    {
        $review->load([
            'reviewer.profile',
            'reviewed.profile',
            'bookingRequest',
            'reports.reporter',
            'moderatedBy',
        ]);

        return Inertia::render('Admin/Moderation/ReviewDetail', [
            'review' => $review,
        ]);
    }

    /**
     * Voir les détails d'un signalement
     */
    public function showReport(ReviewReport $report): Response
    {
        $report->load([
            'review.reviewer.profile',
            'review.reviewed.profile',
            'reporter.profile',
            'reviewer.profile',
        ]);

        return Inertia::render('Admin/Moderation/ReportDetail', [
            'report' => $report,
        ]);
    }

    /**
     * Historique de modération
     */
    public function history(): Response
    {
        $reviews = Review::moderated()
            ->with(['reviewer', 'reviewed', 'moderatedBy'])
            ->orderBy('moderated_at', 'desc')
            ->paginate(20);

        $reports = ReviewReport::reviewed()
            ->with(['review', 'reporter', 'reviewer'])
            ->orderBy('reviewed_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Moderation/History', [
            'reviews' => $reviews,
            'reports' => $reports,
        ]);
    }
}
