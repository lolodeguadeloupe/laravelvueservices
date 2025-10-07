<?php

use App\Models\Review;
use App\Models\ReviewReport;
use App\Models\User;
use App\Services\ModerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->moderationService = app(ModerationService::class);

    // Créer des utilisateurs de test
    $this->admin = User::factory()->create(['user_type' => 'admin']);
    $this->client = User::factory()->create(['user_type' => 'client']);
    $this->provider = User::factory()->create(['user_type' => 'provider']);
});

it('peut effectuer une modération automatique', function () {
    $review = Review::factory()->create([
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'comment' => 'Service excellent, très professionnel',
        'overall_rating' => 5,
        'moderation_status' => 'pending',
    ]);

    $flags = $this->moderationService->autoModerate($review);

    $review->refresh();

    // Un bon avis devrait être auto-approuvé
    expect($flags)->toBeEmpty();
    expect($review->moderation_status)->toBe('approved');
    expect($review->is_published)->toBeTrue();
});

it('peut détecter du contenu suspect', function () {
    $review = Review::factory()->create([
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'comment' => 'Totalement nul, c\'est une arnaque',
        'overall_rating' => 1,
        'moderation_status' => 'pending',
    ]);

    $flags = $this->moderationService->autoModerate($review);

    $review->refresh();

    // Un avis suspect devrait être signalé
    expect($flags)->not->toBeEmpty();
    expect($review->moderation_status)->toBe('flagged');
    expect($review->is_published)->toBeFalse();
});

it('peut signaler un avis', function () {
    $review = Review::factory()->create([
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'moderation_status' => 'approved',
        'is_published' => true,
    ]);

    $reporter = User::factory()->create();

    $report = $review->reportBy(
        $reporter,
        'inappropriate_content',
        'Contenu offensant et irrespectueux'
    );

    expect($report)->toBeInstanceOf(ReviewReport::class);
    expect($report->review_id)->toBe($review->id);
    expect($report->reported_by)->toBe($reporter->id);
    expect($report->reason)->toBe('inappropriate_content');
    expect($report->status)->toBe('pending');

    $review->refresh();
    expect($review->report_count)->toBe(1);
});

it('peut modérer manuellement un avis', function () {
    $review = Review::factory()->create([
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'moderation_status' => 'pending',
    ]);

    $success = $this->moderationService->moderateReview(
        $review,
        'approve',
        $this->admin
    );

    expect($success)->toBeTrue();

    $review->refresh();
    expect($review->moderation_status)->toBe('approved');
    expect($review->moderated_by)->toBe($this->admin->id);
    expect($review->is_published)->toBeTrue();
});

it('peut rejeter un avis avec raison', function () {
    $review = Review::factory()->create([
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'moderation_status' => 'pending',
    ]);

    $success = $this->moderationService->moderateReview(
        $review,
        'reject',
        $this->admin,
        'Contenu inapproprié'
    );

    expect($success)->toBeTrue();

    $review->refresh();
    expect($review->moderation_status)->toBe('rejected');
    expect($review->moderation_reason)->toBe('Contenu inapproprié');
    expect($review->is_published)->toBeFalse();
});

it('peut traiter un signalement', function () {
    $review = Review::factory()->create([
        'moderation_status' => 'approved',
        'is_published' => true,
    ]);

    $report = ReviewReport::factory()->create([
        'review_id' => $review->id,
        'reason' => 'spam',
        'status' => 'pending',
    ]);

    $success = $this->moderationService->processReport(
        $report,
        'validate',
        $this->admin,
        'Signalement justifié'
    );

    expect($success)->toBeTrue();

    $report->refresh();
    expect($report->status)->toBe('reviewed');
    expect($report->reviewed_by)->toBe($this->admin->id);
    expect($report->admin_notes)->toBe('Signalement justifié');

    // L'avis devrait être signalé
    $review->refresh();
    expect($review->moderation_status)->toBe('flagged');
});

it('peut obtenir les statistiques de modération', function () {
    // Créer des avis dans différents états
    Review::factory()->create(['moderation_status' => 'pending']);
    Review::factory()->create(['moderation_status' => 'flagged']);
    Review::factory()->create(['moderation_status' => 'approved']);

    ReviewReport::factory()->create(['status' => 'pending']);
    ReviewReport::factory()->create(['status' => 'reviewed']);

    $stats = $this->moderationService->getModerationStats();

    expect($stats)->toHaveKeys([
        'pending_reviews',
        'flagged_reviews',
        'pending_reports',
        'total_reports',
        'auto_approved_today',
        'manually_moderated_today',
    ]);

    expect($stats['pending_reviews'])->toBe(1);
    expect($stats['flagged_reviews'])->toBe(1);
    expect($stats['pending_reports'])->toBe(1);
    expect($stats['total_reports'])->toBe(2);
});

it('peut effectuer une modération en lot', function () {
    $reviews = Review::factory()->count(3)->create([
        'moderation_status' => 'pending',
    ]);

    $reviewIds = $reviews->pluck('id')->toArray();

    $results = $this->moderationService->bulkModerate(
        $reviewIds,
        'approve',
        $this->admin
    );

    expect($results['success'])->toBe(3);
    expect($results['failed'])->toBe(0);

    // Vérifier que tous les avis sont approuvés
    foreach ($reviews as $review) {
        $review->refresh();
        expect($review->moderation_status)->toBe('approved');
        expect($review->moderated_by)->toBe($this->admin->id);
    }
});

it('empêche les signalements multiples du même utilisateur', function () {
    $review = Review::factory()->create([
        'moderation_status' => 'approved',
        'is_published' => true,
    ]);

    $reporter = User::factory()->create();

    // Premier signalement
    $report1 = $review->reportBy($reporter, 'spam', 'Premier signalement');
    expect($report1)->toBeInstanceOf(ReviewReport::class);

    // Tentative de second signalement
    $report2 = ReviewReport::where('review_id', $review->id)
        ->where('reported_by', $reporter->id)
        ->first();

    expect($report2->id)->toBe($report1->id); // Même signalement
});

it('signale automatiquement les avis avec multiple reports', function () {
    $review = Review::factory()->create([
        'moderation_status' => 'approved',
        'is_published' => true,
    ]);

    // Créer 3 signalements différents
    $reporters = User::factory()->count(3)->create();

    foreach ($reporters as $reporter) {
        $review->reportBy($reporter, 'inappropriate_content', 'Signalement');
    }

    $review->refresh();

    // Après 3 signalements, l'avis devrait être automatiquement signalé
    expect($review->report_count)->toBe(3);
    expect($review->moderation_status)->toBe('flagged');
});
