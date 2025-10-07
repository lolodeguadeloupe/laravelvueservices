<?php

namespace Database\Factories;

use App\Models\BookingRequest;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $overallRating = $this->faker->numberBetween(1, 5);

        return [
            'booking_request_id' => BookingRequest::factory(),
            'reviewer_id' => User::factory(),
            'reviewed_id' => User::factory(),
            'reviewer_type' => $this->faker->randomElement(['client', 'provider']),
            'overall_rating' => $overallRating,
            'quality_rating' => $this->faker->optional(0.8)->numberBetween(1, 5),
            'communication_rating' => $this->faker->optional(0.8)->numberBetween(1, 5),
            'punctuality_rating' => $this->faker->optional(0.8)->numberBetween(1, 5),
            'professionalism_rating' => $this->faker->optional(0.8)->numberBetween(1, 5),
            'value_rating' => $this->faker->optional(0.8)->numberBetween(1, 5),
            'title' => $this->faker->optional(0.7)->sentence(),
            'comment' => $this->faker->paragraph(),
            'photos' => $this->faker->optional(0.3)->randomElements([
                'reviews/photo1.jpg',
                'reviews/photo2.jpg',
                'reviews/photo3.jpg',
            ], $this->faker->numberBetween(1, 3)),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'is_verified' => $this->faker->boolean(80),
            'is_featured' => $this->faker->boolean(10),
            'helpful_count' => $this->faker->numberBetween(0, 50),
            'not_helpful_count' => $this->faker->numberBetween(0, 10),
            'response' => $this->faker->optional(0.3)->paragraph(),
            'response_by' => function (array $attributes) {
                return $attributes['response'] ? User::factory() : null;
            },
            'response_at' => function (array $attributes) {
                return $attributes['response'] ? $this->faker->dateTimeBetween('-1 month', 'now') : null;
            },
            'approved_by' => function (array $attributes) {
                return $attributes['status'] === 'approved' ? User::factory() : null;
            },
            'approved_at' => function (array $attributes) {
                return $attributes['status'] === 'approved' ? $this->faker->dateTimeBetween('-1 month', 'now') : null;
            },
            'rejection_reason' => function (array $attributes) {
                return $attributes['status'] === 'rejected' ? $this->faker->sentence() : null;
            },
            'rejected_by' => function (array $attributes) {
                return $attributes['status'] === 'rejected' ? User::factory() : null;
            },
            'rejected_at' => function (array $attributes) {
                return $attributes['status'] === 'rejected' ? $this->faker->dateTimeBetween('-1 month', 'now') : null;
            },
        ];
    }

    /**
     * Indicate that the review is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by' => User::factory(),
            'approved_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the review is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Indicate that the review is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejection_reason' => $this->faker->sentence(),
            'rejected_by' => User::factory(),
            'rejected_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the review is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
        ]);
    }

    /**
     * Indicate that the review is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the review has a response.
     */
    public function withResponse(): static
    {
        return $this->state(fn (array $attributes) => [
            'response' => $this->faker->paragraph(),
            'response_by' => User::factory(),
            'response_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the review has photos.
     */
    public function withPhotos(?int $count = null): static
    {
        $photoCount = $count ?? $this->faker->numberBetween(1, 5);
        $photos = [];

        for ($i = 0; $i < $photoCount; $i++) {
            $photos[] = "reviews/photo_{$i}.jpg";
        }

        return $this->state(fn (array $attributes) => [
            'photos' => $photos,
        ]);
    }

    /**
     * Indicate that the review is from a client.
     */
    public function fromClient(): static
    {
        return $this->state(fn (array $attributes) => [
            'reviewer_type' => 'client',
        ]);
    }

    /**
     * Indicate that the review is from a provider.
     */
    public function fromProvider(): static
    {
        return $this->state(fn (array $attributes) => [
            'reviewer_type' => 'provider',
        ]);
    }

    /**
     * Indicate a specific rating.
     */
    public function rating(int $rating): static
    {
        return $this->state(fn (array $attributes) => [
            'overall_rating' => $rating,
        ]);
    }
}
