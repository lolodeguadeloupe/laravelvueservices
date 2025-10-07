<?php

use App\Models\BookingRequest;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Exécuter les migrations de permissions
    $this->artisan('migrate', ['--path' => 'database/migrations/2025_10_07_084631_create_permission_tables.php']);
    
    // Créer les rôles
    Role::create(['name' => 'client', 'guard_name' => 'web']);
    Role::create(['name' => 'provider', 'guard_name' => 'web']);
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
});

test('client can access payment page for accepted booking', function () {
    $client = User::factory()->create(['user_type' => 'client']);
    $client->assignRole('client');
    
    $provider = User::factory()->create(['user_type' => 'provider']);
    $provider->assignRole('provider');
    
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'category_id' => $category->id,
        'provider_id' => $provider->id,
    ]);
    
    $booking = BookingRequest::factory()->create([
        'service_id' => $service->id,
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'status' => 'accepted',
        'final_price' => 100.00,
    ]);

    $response = $this->actingAs($client)->get(route('payments.create', $booking));
    
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Payments/Create')
        ->has('booking')
        ->has('stripeKey')
    );
});

test('client cannot pay pending booking', function () {
    $client = User::factory()->create(['user_type' => 'client']);
    $client->assignRole('client');
    
    $provider = User::factory()->create(['user_type' => 'provider']);
    $provider->assignRole('provider');
    
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'category_id' => $category->id,
        'provider_id' => $provider->id,
    ]);
    
    $booking = BookingRequest::factory()->create([
        'service_id' => $service->id,
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'status' => 'pending', // Pas encore accepté
        'quoted_price' => 100.00,
    ]);

    $response = $this->actingAs($client)->get(route('payments.create', $booking));
    
    $response->assertForbidden();
});

test('payment calculates platform fee correctly', function () {
    $payment = new Payment([
        'amount' => 100.00,
    ]);
    
    $payment->calculatePlatformFee(0.15); // 15%
    
    expect($payment->platform_fee)->toBe(15.00);
    expect($payment->provider_amount)->toBe(85.00);
});

test('payment creation generates uuid', function () {
    $client = User::factory()->create(['user_type' => 'client']);
    $provider = User::factory()->create(['user_type' => 'provider']);
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'category_id' => $category->id,
        'provider_id' => $provider->id,
    ]);
    
    $booking = BookingRequest::factory()->create([
        'service_id' => $service->id,
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'status' => 'accepted',
        'final_price' => 100.00,
    ]);

    $payment = Payment::create([
        'booking_request_id' => $booking->id,
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'amount' => 100.00,
        'platform_fee' => 15.00,
        'provider_amount' => 85.00,
    ]);

    expect($payment->uuid)->not->toBeNull();
    expect($payment->uuid)->toBeString();
});

test('provider cannot access payment page', function () {
    $client = User::factory()->create(['user_type' => 'client']);
    $client->assignRole('client');
    
    $provider = User::factory()->create(['user_type' => 'provider']);
    $provider->assignRole('provider');
    
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'category_id' => $category->id,
        'provider_id' => $provider->id,
    ]);
    
    $booking = BookingRequest::factory()->create([
        'service_id' => $service->id,
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'status' => 'accepted',
        'final_price' => 100.00,
    ]);

    $response = $this->actingAs($provider)->get(route('payments.create', $booking));
    
    $response->assertForbidden();
});