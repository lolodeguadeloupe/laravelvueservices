<?php

use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('verified provider service can be accessed', function () {
    // Create a verified provider
    $provider = User::factory()->create([
        'user_type' => 'provider',
        'verification_status' => 'verified',
        'email_verified_at' => now(),
    ]);

    // Create category and service
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'provider_id' => $provider->id,
        'category_id' => $category->id,
        'is_active' => true,
    ]);

    // Access service page
    $response = $this->get(route('services.show', $service));
    $response->assertOk();
});

test('pending provider service returns 404', function () {
    // Create a pending provider
    $provider = User::factory()->create([
        'user_type' => 'provider',
        'verification_status' => 'pending',
        'email_verified_at' => now(),
    ]);

    // Create category and service
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'provider_id' => $provider->id,
        'category_id' => $category->id,
        'is_active' => true,
    ]);

    // Access service page should return 404
    $response = $this->get(route('services.show', $service));
    $response->assertNotFound();
});

test('inactive service returns 404', function () {
    // Create a verified provider
    $provider = User::factory()->create([
        'user_type' => 'provider',
        'verification_status' => 'verified',
        'email_verified_at' => now(),
    ]);

    // Create category and inactive service
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'provider_id' => $provider->id,
        'category_id' => $category->id,
        'is_active' => false,
    ]);

    // Access service page should return 404
    $response = $this->get(route('services.show', $service));
    $response->assertNotFound();
});