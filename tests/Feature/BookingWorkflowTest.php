<?php

use App\Models\BookingRequest;
use App\Models\Category;
use App\Models\Message;
use App\Models\Service;
use App\Models\User;
use App\Notifications\BookingStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Créer les rôles
    Role::create(['name' => 'client', 'guard_name' => 'web']);
    Role::create(['name' => 'provider', 'guard_name' => 'web']);
    
    // Créer une catégorie
    $this->category = Category::factory()->create();
    
    // Créer les utilisateurs
    $this->client = User::factory()->create();
    $this->client->assignRole('client');
    
    $this->provider = User::factory()->create();
    $this->provider->assignRole('provider');
    
    // Créer un service
    $this->service = Service::factory()->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
    ]);
});

test('complete booking workflow', function () {
    Notification::fake();

    // 1. Client crée une demande
    $this->actingAs($this->client);
    
    $bookingData = [
        'service_id' => $this->service->id,
        'preferred_datetime' => now()->addDays(3)->toDateTimeString(),
        'client_address' => [
            'street' => '123 Rue Test',
            'city' => 'Paris',
            'postal_code' => '75001',
        ],
        'client_notes' => 'Notes du client',
    ];

    $response = $this->post(route('bookings.store'), $bookingData);
    $response->assertRedirect();

    $booking = BookingRequest::where('client_id', $this->client->id)->first();
    expect($booking)->not->toBeNull();
    expect($booking->status)->toBe('pending');

    // 2. Prestataire envoie un devis
    $this->actingAs($this->provider);
    
    $quoteData = [
        'quoted_price' => 150.00,
        'estimated_duration' => 120,
        'confirmed_datetime' => now()->addDays(3)->toDateTimeString(),
        'provider_notes' => 'Devis détaillé',
    ];

    $response = $this->patch(route('bookings.quote', $booking), $quoteData);
    $response->assertRedirect();

    $booking->refresh();
    expect($booking->status)->toBe('quoted');
    expect($booking->quoted_price)->toBe('150.00');

    // 3. Prestataire accepte la demande
    $response = $this->patch(route('bookings.accept', $booking));
    $response->assertRedirect();

    $booking->refresh();
    expect($booking->status)->toBe('accepted');
    expect($booking->accepted_at)->not->toBeNull();

    // 4. Prestataire démarre l'intervention
    $startData = [
        'provider_location' => [
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ],
        'before_photos' => ['photo1.jpg'],
    ];

    $response = $this->patch(route('bookings.start-intervention', $booking), $startData);
    $response->assertRedirect();

    $booking->refresh();
    expect($booking->status)->toBe('in_progress');
    expect($booking->started_at)->not->toBeNull();

    // 5. Prestataire termine l'intervention
    $finishData = [
        'work_summary' => 'Travail effectué avec succès',
        'after_photos' => ['photo2.jpg'],
        'final_price' => 150.00,
        'intervention_report' => ['materials' => 'Matériaux utilisés'],
    ];

    $response = $this->patch(route('bookings.finish-intervention', $booking), $finishData);
    $response->assertRedirect();

    $booking->refresh();
    expect($booking->status)->toBe('completed');
    expect($booking->finished_at)->not->toBeNull();
    expect($booking->completed_at)->not->toBeNull();

    // Vérifier l'historique des statuts
    $this->assertDatabaseCount('booking_status_history', 5);
});

test('messaging between client and provider', function () {
    $booking = BookingRequest::factory()->create([
        'client_id' => $this->client->id,
        'provider_id' => $this->provider->id,
        'service_id' => $this->service->id,
    ]);

    // Client envoie un message
    $this->actingAs($this->client);
    
    $messageData = [
        'content' => 'Bonjour, j\'ai une question',
        'type' => 'text',
    ];

    $response = $this->post(route('bookings.messages.store', $booking), $messageData);
    $response->assertStatus(201);

    $message = Message::where('booking_request_id', $booking->id)->first();
    expect($message)->not->toBeNull();
    expect($message->sender_id)->toBe($this->client->id);
    expect($message->receiver_id)->toBe($this->provider->id);

    // Prestataire lit les messages
    $this->actingAs($this->provider);
    
    $response = $this->get(route('bookings.messages.index', $booking));
    $response->assertStatus(200);

    // Vérifier que le message est marqué comme lu
    $message->refresh();
    expect($message->read_at)->not->toBeNull();
});

test('booking cancellation', function () {
    $booking = BookingRequest::factory()->create([
        'client_id' => $this->client->id,
        'provider_id' => $this->provider->id,
        'service_id' => $this->service->id,
        'status' => 'accepted',
    ]);

    $this->actingAs($this->client);
    
    $cancelData = [
        'cancellation_reason' => 'Changement de plans',
    ];

    $response = $this->patch(route('bookings.cancel', $booking), $cancelData);
    $response->assertRedirect();

    $booking->refresh();
    expect($booking->status)->toBe('cancelled');
    expect($booking->cancelled_by)->toBe($this->client->id);
    expect($booking->cancelled_at)->not->toBeNull();
});