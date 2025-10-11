<?php

use App\Models\BookingRequest;
use App\Models\Category;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can access reviews through booking requests', function () {
    // Create a provider and client
    $provider = User::factory()->create(['user_type' => 'provider']);
    $client = User::factory()->create(['user_type' => 'client']);

    // Create a category and service
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'provider_id' => $provider->id,
        'category_id' => $category->id,
    ]);

    // Create a booking request
    $booking = BookingRequest::factory()->create([
        'service_id' => $service->id,
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'status' => 'completed',
    ]);

    // Create a review manually to avoid factory issues
    $review = Review::create([
        'booking_request_id' => $booking->id,
        'reviewer_id' => $client->id,
        'reviewed_id' => $provider->id,
        'reviewer_type' => 'client',
        'overall_rating' => 5,
        'status' => 'approved',
        'title' => 'Great service',
        'comment' => 'Very satisfied with the service',
    ]);

    // Test that the service can access its reviews
    expect($service->reviews)->toHaveCount(1);
    expect($service->reviews->first()->id)->toBe($review->id);
    expect($service->reviews->first()->overall_rating)->toBe(5);
});

it('returns empty collection when service has no reviews', function () {
    $provider = User::factory()->create(['user_type' => 'provider']);
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'provider_id' => $provider->id,
        'category_id' => $category->id,
    ]);

    expect($service->reviews)->toHaveCount(0);
    expect($service->reviews)->toBeEmpty();
});

it('can calculate average rating from reviews', function () {
    // Create a provider and client
    $provider = User::factory()->create(['user_type' => 'provider']);
    $client = User::factory()->create(['user_type' => 'client']);

    // Create a category and service
    $category = Category::factory()->create();
    $service = Service::factory()->create([
        'provider_id' => $provider->id,
        'category_id' => $category->id,
    ]);

    // Create multiple booking requests with reviews
    $bookings = BookingRequest::factory()->count(3)->create([
        'service_id' => $service->id,
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'status' => 'completed',
    ]);

    // Create reviews with different ratings
    Review::create([
        'booking_request_id' => $bookings[0]->id,
        'reviewer_id' => $client->id,
        'reviewed_id' => $provider->id,
        'reviewer_type' => 'client',
        'overall_rating' => 5,
        'status' => 'approved',
        'comment' => 'Excellent service',
    ]);

    Review::create([
        'booking_request_id' => $bookings[1]->id,
        'reviewer_id' => $client->id,
        'reviewed_id' => $provider->id,
        'reviewer_type' => 'client',
        'overall_rating' => 4,
        'status' => 'approved',
        'comment' => 'Good service',
    ]);

    Review::create([
        'booking_request_id' => $bookings[2]->id,
        'reviewer_id' => $client->id,
        'reviewed_id' => $provider->id,
        'reviewer_type' => 'client',
        'overall_rating' => 3,
        'status' => 'approved',
        'comment' => 'Average service',
    ]);

    // Test withAvg on overall_rating (should work correctly)
    $serviceWithAvg = Service::where('id', $service->id)
        ->withAvg('reviews', 'overall_rating')
        ->first();

    expect($serviceWithAvg->reviews_avg_overall_rating)->toBe(4.0); // (5+4+3)/3 = 4.0
});
