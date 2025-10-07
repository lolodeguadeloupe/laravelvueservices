<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewReport;
use Illuminate\Http\Request;

class ReviewReportController extends Controller
{
    /**
     * Signaler un avis
     */
    public function store(Request $request, Review $review): array
    {
        $request->validate([
            'reason' => 'required|in:inappropriate_content,spam,false_information,harassment,fake_review,other',
            'description' => 'nullable|string|max:500',
        ]);

        // Vérifier si l'utilisateur a déjà signalé cet avis
        $existingReport = ReviewReport::where('review_id', $review->id)
            ->where('reported_by', auth()->id())
            ->first();

        if ($existingReport) {
            return [
                'error' => 'Vous avez déjà signalé cet avis.',
            ];
        }

        // Vérifier que l'utilisateur ne signale pas son propre avis
        if ($review->reviewer_id === auth()->id()) {
            return [
                'error' => 'Vous ne pouvez pas signaler votre propre avis.',
            ];
        }

        $report = $review->reportBy(
            auth()->user(),
            $request->reason,
            $request->description
        );

        return [
            'message' => 'Avis signalé avec succès. Merci de nous aider à maintenir la qualité de la plateforme.',
            'report' => $report,
        ];
    }

    /**
     * Obtenir les raisons de signalement disponibles
     */
    public function reasons(): array
    {
        return [
            'reasons' => [
                'inappropriate_content' => 'Contenu inapproprié',
                'spam' => 'Spam/Publicité',
                'false_information' => 'Fausses informations',
                'harassment' => 'Harcèlement',
                'fake_review' => 'Faux avis',
                'other' => 'Autre',
            ],
        ];
    }

    /**
     * Voir mes signalements
     */
    public function index(): array
    {
        $reports = ReviewReport::where('reported_by', auth()->id())
            ->with(['review.reviewer', 'review.reviewed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'reports' => $reports,
        ];
    }
}
