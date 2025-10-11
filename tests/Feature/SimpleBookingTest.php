<?php

use App\Models\BookingRequest;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('can create a simple booking request', function () {
    // Create roles
    Role::create(['name' => 'client', 'guard_name' => 'web']);
    Role::create(['name' => 'provider', 'guard_name' => 'web']);

    // Create users
    $client = User::factory()->create(['email_verified_at' => now()]);
    $client->assignRole('client');

    $provider = User::factory()->create(['email_verified_at' => now()]);
    $provider->assignRole('provider');

    // Create category and service
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'provider_id' => $provider->id,
        'category_id' => $category->id,
    ]);

    // Create booking request directly (to avoid web interface complications)
    $booking = BookingRequest::create([
        'service_id' => $service->id,
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'status' => 'pending',
        'preferred_datetime' => now()->addDays(2),
        'client_address' => [
            'street' => '123 Test Street',
            'city' => 'Paris',
            'postal_code' => '75001',
        ],
        'client_notes' => 'Test booking',
    ]);

    expect($booking)->toBeInstanceOf(BookingRequest::class);
    expect($booking->status)->toBe('pending');
    expect($booking->service_id)->toBe($service->id);
    expect($booking->client_id)->toBe($client->id);
    expect($booking->provider_id)->toBe($provider->id);
});

it('can accept a booking request via HTTP', function () {
    // Fake notifications to avoid database issues
    Notification::fake();

    // Create roles
    Role::create(['name' => 'client', 'guard_name' => 'web']);
    Role::create(['name' => 'provider', 'guard_name' => 'web']);

    // Create users
    $client = User::factory()->create(['email_verified_at' => now()]);
    $client->assignRole('client');

    $provider = User::factory()->create(['email_verified_at' => now()]);
    $provider->assignRole('provider');

    // Create category and service
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'provider_id' => $provider->id,
        'category_id' => $category->id,
    ]);

    // Create booking request
    $booking = BookingRequest::create([
        'service_id' => $service->id,
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'status' => 'pending',
        'preferred_datetime' => now()->addDays(2),
        'client_address' => [
            'street' => '123 Test Street',
            'city' => 'Paris',
            'postal_code' => '75001',
        ],
        'client_notes' => 'Test booking',
    ]);

    // Test accepting the booking via HTTP (this should test for BindingResolutionException)
    $this->actingAs($provider);

    $response = $this->patch(route('bookings.accept', $booking));

    $response->assertRedirect();

    $booking->refresh();
    expect($booking->status)->toBe('accepted');
    expect($booking->accepted_at)->not->toBeNull();
});
