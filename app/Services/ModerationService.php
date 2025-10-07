<?php

namespace App\Services;

use App\Models\Review;
use App\Models\ReviewReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ModerationService
{
    /**
     * Mots-clés suspects pour la modération automatique
     */
    private array $suspiciousKeywords = [
        'spam', 'escroquerie', 'arnaque', 'vol', 'fraude',
        'incompétent', 'nul', 'pourri', 'terrible', 'horrible',
        'discrimination', 'raciste', 'sexiste', 'harcèlement',
    ];

    /**
     * Effectuer une modération automatique sur un avis
     */
    public function autoModerate(Review $review): array
    {
        $flags = [];
        $content = strtolower($review->comment ?? '');

        // Vérifier les mots-clés suspects
        foreach ($this->suspiciousKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                $flags['suspicious_keywords'][] = $keyword;
            }
        }

        // Vérifier la longueur du contenu
        if (strlen(trim($review->comment ?? '')) < 10) {
            $flags['too_short'] = true;
        }

        // Vérifier les notes extrêmes avec peu de contenu
        if ($review->overall_rating <= 2 && strlen(trim($review->comment ?? '')) < 50) {
            $flags['extreme_rating_low_content'] = true;
        }

        // Vérifier les répétitions suspectes
        if ($this->hasRepetitiveContent($content)) {
            $flags['repetitive_content'] = true;
        }

        // Vérifier l'historique du reviewer
        $reviewerFlags = $this->checkReviewerHistory($review->reviewer);
        if (! empty($reviewerFlags)) {
            $flags['reviewer_history'] = $reviewerFlags;
        }

        // Si des flags sont détectés, marquer pour modération
        if (! empty($flags)) {
            $review->flag($flags);
            Log::info("Avis {$review->id} signalé automatiquement", $flags);
        } else {
            // Auto-approuver si aucun flag
            $review->approve();
        }

        return $flags;
    }

    /**
     * Obtenir les avis en attente de modération
     */
    public function getPendingReviews(): Collection
    {
        return Review::pendingModeration()
            ->with(['reviewer', 'reviewed', 'bookingRequest'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Obtenir les avis signalés
     */
    public function getFlaggedReviews(): Collection
    {
        return Review::flagged()
            ->with(['reviewer', 'reviewed', 'reports.reporter'])
            ->orderBy('report_count', 'desc')
            ->orderBy('moderated_at', 'asc')
            ->get();
    }

    /**
     * Obtenir les signalements en attente
     */
    public function getPendingReports(): Collection
    {
        return ReviewReport::pending()
            ->with(['review.reviewer', 'review.reviewed', 'reporter'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Modérer un avis manuellement
     */
    public function moderateReview(Review $review, string $action, User $moderator, ?string $reason = null): bool
    {
        try {
            switch ($action) {
                case 'approve':
                    $review->approve($moderator);
                    break;
                case 'reject':
                    if (! $reason) {
                        throw new \InvalidArgumentException('Une raison est requise pour rejeter un avis');
                    }
                    $review->reject($reason, $moderator);
                    break;
                case 'flag':
                    $review->flag(['manual_flag' => $reason], $moderator);
                    break;
                default:
                    throw new \InvalidArgumentException("Action inconnue: {$action}");
            }

            Log::info("Avis {$review->id} modéré par {$moderator->name}", [
                'action' => $action,
                'reason' => $reason,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la modération de l'avis {$review->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Traiter un signalement
     */
    public function processReport(ReviewReport $report, string $action, User $moderator, ?string $adminNotes = null): bool
    {
        try {
            $report->update([
                'status' => $action === 'dismiss' ? 'dismissed' : 'reviewed',
                'reviewed_by' => $moderator->id,
                'reviewed_at' => now(),
                'admin_notes' => $adminNotes,
            ]);

            // Si le signalement est validé, prendre des mesures sur l'avis
            if ($action === 'validate') {
                $this->moderateReview($report->review, 'flag', $moderator, "Signalement validé: {$report->reason_label}");
            }

            Log::info("Signalement {$report->id} traité par {$moderator->name}", [
                'action' => $action,
                'admin_notes' => $adminNotes,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors du traitement du signalement {$report->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Obtenir les statistiques de modération
     */
    public function getModerationStats(): array
    {
        return [
            'pending_reviews' => Review::pendingModeration()->count(),
            'flagged_reviews' => Review::flagged()->count(),
            'pending_reports' => ReviewReport::pending()->count(),
            'total_reports' => ReviewReport::count(),
            'auto_approved_today' => Review::where('moderation_status', 'approved')
                ->where('moderated_by', null)
                ->whereDate('moderated_at', today())
                ->count(),
            'manually_moderated_today' => Review::moderated()
                ->whereNotNull('moderated_by')
                ->whereDate('moderated_at', today())
                ->count(),
        ];
    }

    /**
     * Vérifier si le contenu est répétitif
     */
    private function hasRepetitiveContent(string $content): bool
    {
        $words = explode(' ', $content);
        $wordCount = array_count_values($words);

        // Si un mot est répété plus de 5 fois, c'est suspect
        foreach ($wordCount as $count) {
            if ($count > 5) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifier l'historique du reviewer
     */
    private function checkReviewerHistory(User $reviewer): array
    {
        $flags = [];

        // Vérifier le nombre d'avis rejetés récemment
        $rejectedCount = Review::where('reviewer_id', $reviewer->id)
            ->where('moderation_status', 'rejected')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($rejectedCount >= 3) {
            $flags['frequent_rejections'] = $rejectedCount;
        }

        // Vérifier si c'est un nouveau compte
        if ($reviewer->created_at >= now()->subDays(7)) {
            $flags['new_account'] = true;
        }

        // Vérifier la fréquence des avis
        $recentReviews = Review::where('reviewer_id', $reviewer->id)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        if ($recentReviews > 5) {
            $flags['high_frequency'] = $recentReviews;
        }

        return $flags;
    }

    /**
     * Effectuer une modération en lot
     */
    public function bulkModerate(array $reviewIds, string $action, User $moderator, ?string $reason = null): array
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];

        foreach ($reviewIds as $reviewId) {
            $review = Review::find($reviewId);
            if (! $review) {
                $results['failed']++;
                $results['errors'][] = "Avis {$reviewId} introuvable";

                continue;
            }

            if ($this->moderateReview($review, $action, $moderator, $reason)) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "Échec modération avis {$reviewId}";
            }
        }

        return $results;
    }
}
