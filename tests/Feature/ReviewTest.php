<?php

use App\Models\BookingRequest;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->artisan('migrate:fresh');

    Storage::fake('public');

    // Créer les utilisateurs
    $this->client = User::factory()->create(['user_type' => 'client']);
    $this->provider = User::factory()->create(['user_type' => 'provider']);
    $this->admin = User::factory()->create(['user_type' => 'admin']);

    // Créer les profils
    UserProfile::create([
        'user_id' => $this->client->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'bio' => 'Client test profile',
    ]);
    UserProfile::create([
        'user_id' => $this->provider->id,
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'bio' => 'Provider test profile',
    ]);

    // Créer un service
    $this->service = Service::factory()->create(['provider_id' => $this->provider->id]);

    // Créer une réservation terminée
    $this->booking = BookingRequest::factory()->create([
        'client_id' => $this->client->id,
        'provider_id' => $this->provider->id,
        'service_id' => $this->service->id,
        'status' => 'completed',
    ]);
});

it('can display reviews index page', function () {
    $review = Review::factory()->create([
        'booking_request_id' => $this->booking->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'status' => 'approved',
    ]);

    $response = $this->get(route('reviews.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page->component('Reviews/Index')
        ->has('reviews.data', 1)
    );
});

it('can show a specific review', function () {
    $review = Review::factory()->create([
        'booking_request_id' => $this->booking->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'status' => 'approved',
    ]);

    $response = $this->get(route('reviews.show', $review));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page->component('Reviews/Show')
        ->where('review.id', $review->id)
    );
});

it('can create a review page for completed booking', function () {
    $response = $this->actingAs($this->client)
        ->get(route('reviews.create', $this->booking));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page->component('Reviews/Create')
        ->where('booking.id', $this->booking->id)
        ->where('reviewerType', 'client')
    );
});

it('cannot create review for non-completed booking', function () {
    $this->booking->update(['status' => 'pending']);

    $response = $this->actingAs($this->client)
        ->get(route('reviews.create', $this->booking));

    $response->assertForbidden();
});

it('can store a review', function () {
    $reviewData = [
        'overall_rating' => 5,
        'quality_rating' => 5,
        'communication_rating' => 4,
        'title' => 'Excellent service',
        'comment' => 'Le service était vraiment excellent, je recommande vivement.',
    ];

    $response = $this->actingAs($this->client)
        ->postJson(route('reviews.store', $this->booking), $reviewData);

    $response->assertSuccessful();
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('reviews', [
        'booking_request_id' => $this->booking->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'overall_rating' => 5,
        'title' => 'Excellent service',
        'comment' => 'Le service était vraiment excellent, je recommande vivement.',
    ]);
});

it('can store a review with photos', function () {
    $photo1 = UploadedFile::fake()->image('photo1.jpg');
    $photo2 = UploadedFile::fake()->image('photo2.jpg');

    $reviewData = [
        'overall_rating' => 4,
        'comment' => 'Bon service avec photos',
        'photos' => [$photo1, $photo2],
    ];

    $response = $this->actingAs($this->client)
        ->post(route('reviews.store', $this->booking), $reviewData);

    $response->assertSuccessful();

    $review = Review::where('booking_request_id', $this->booking->id)->first();
    expect($review->photos)->toHaveCount(2);

    // Vérifier que les fichiers ont été stockés
    Storage::disk('public')->assertExists($review->photos[0]);
    Storage::disk('public')->assertExists($review->photos[1]);
});

it('validates review data', function () {
    $response = $this->actingAs($this->client)
        ->postJson(route('reviews.store', $this->booking), []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['overall_rating', 'comment']);
});

it('validates photo uploads', function () {
    $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

    $reviewData = [
        'overall_rating' => 4,
        'comment' => 'Test review',
        'photos' => [$invalidFile],
    ];

    $response = $this->actingAs($this->client)
        ->post(route('reviews.store', $this->booking), $reviewData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['photos.0']);
});

it('cannot create duplicate review', function () {
    // Créer un premier avis
    Review::factory()->create([
        'booking_request_id' => $this->booking->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
    ]);

    $reviewData = [
        'overall_rating' => 3,
        'comment' => 'Second review attempt',
    ];

    $response = $this->actingAs($this->client)
        ->postJson(route('reviews.store', $this->booking), $reviewData);

    $response->assertStatus(422);
    $response->assertJson(['error' => 'Vous avez déjà laissé un avis pour cette réservation.']);
});

it('can mark review as helpful', function () {
    $review = Review::factory()->create([
        'booking_request_id' => $this->booking->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'status' => 'approved',
    ]);

    $otherUser = User::factory()->create();

    $response = $this->actingAs($otherUser)
        ->postJson(route('reviews.helpful', $review));

    $response->assertSuccessful();
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('review_reactions', [
        'review_id' => $review->id,
        'user_id' => $otherUser->id,
        'type' => 'helpful',
    ]);
});

it('cannot mark own review as helpful', function () {
    $review = Review::factory()->create([
        'booking_request_id' => $this->booking->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'status' => 'approved',
    ]);

    $response = $this->actingAs($this->client)
        ->postJson(route('reviews.helpful', $review));

    $response->assertForbidden();
});

it('can respond to review', function () {
    $review = Review::factory()->create([
        'booking_request_id' => $this->booking->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'status' => 'approved',
    ]);

    $response = $this->actingAs($this->provider)
        ->postJson(route('reviews.respond', $review), [
            'response' => 'Merci pour votre avis positif!',
        ]);

    $response->assertSuccessful();

    $review->refresh();
    expect($review->response)->toBe('Merci pour votre avis positif!');
    expect($review->response_by)->toBe($this->provider->id);
});

it('can report review', function () {
    $review = Review::factory()->create([
        'booking_request_id' => $this->booking->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'status' => 'approved',
    ]);

    $otherUser = User::factory()->create();

    $response = $this->actingAs($otherUser)
        ->postJson(route('reviews.report', $review), [
            'reason' => 'inappropriate',
        ]);

    $response->assertSuccessful();

    $this->assertDatabaseHas('review_reactions', [
        'review_id' => $review->id,
        'user_id' => $otherUser->id,
        'type' => 'report',
        'reason' => 'inappropriate',
    ]);
});

it('filters reviews by rating', function () {
    // Créer des avis avec différentes notes
    Review::factory()->create([
        'booking_request_id' => $this->booking->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'overall_rating' => 5,
        'status' => 'approved',
    ]);

    $booking2 = BookingRequest::factory()->create([
        'client_id' => $this->client->id,
        'provider_id' => $this->provider->id,
        'service_id' => $this->service->id,
        'status' => 'completed',
    ]);

    Review::factory()->create([
        'booking_request_id' => $booking2->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'overall_rating' => 3,
        'status' => 'approved',
    ]);

    $response = $this->get(route('reviews.index', ['rating' => 4]));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page->component('Reviews/Index')
        ->has('reviews.data', 1) // Seul l'avis 5 étoiles >= 4
    );
});

it('updates user rating when review is approved', function () {
    $review = Review::factory()->create([
        'booking_request_id' => $this->booking->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'overall_rating' => 4,
        'status' => 'pending',
    ]);

    // Approuver l'avis
    $review->approve($this->admin);

    // Vérifier que le rating du provider a été mis à jour
    $this->provider->refresh();
    expect($this->provider->profile->rating)->toBe(4.0);
});

it('calculates correct average rating with multiple reviews', function () {
    // Créer plusieurs réservations et avis
    $booking2 = BookingRequest::factory()->create([
        'client_id' => $this->client->id,
        'provider_id' => $this->provider->id,
        'service_id' => $this->service->id,
        'status' => 'completed',
    ]);

    $booking3 = BookingRequest::factory()->create([
        'client_id' => $this->client->id,
        'provider_id' => $this->provider->id,
        'service_id' => $this->service->id,
        'status' => 'completed',
    ]);

    // Créer plusieurs avis approuvés
    Review::factory()->create([
        'booking_request_id' => $this->booking->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'overall_rating' => 5,
        'status' => 'approved',
    ]);

    Review::factory()->create([
        'booking_request_id' => $booking2->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'overall_rating' => 3,
        'status' => 'approved',
    ]);

    Review::factory()->create([
        'booking_request_id' => $booking3->id,
        'reviewer_id' => $this->client->id,
        'reviewed_id' => $this->provider->id,
        'overall_rating' => 4,
        'status' => 'approved',
    ]);

    // Mettre à jour le rating
    $this->provider->updateAverageRating();

    // Vérifier le calcul: (5 + 3 + 4) / 3 = 4.0
    $this->provider->refresh();
    expect($this->provider->profile->rating)->toBe(4.0);
});
