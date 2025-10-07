<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReviewReport>
 */
class ReviewReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'review_id' => Review::factory(),
            'reported_by' => User::factory(),
            'reason' => $this->faker->randomElement([
                'inappropriate_content',
                'spam',
                'false_information',
                'harassment',
                'fake_review',
                'other',
            ]),
            'description' => $this->faker->optional()->text(200),
            'status' => 'pending',
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function reviewed(): static
    {
        return $this->state([
            'status' => 'reviewed',
            'reviewed_by' => User::factory(),
            'reviewed_at' => now(),
            'admin_notes' => $this->faker->optional()->text(100),
        ]);
    }

    public function dismissed(): static
    {
        return $this->state([
            'status' => 'dismissed',
            'reviewed_by' => User::factory(),
            'reviewed_at' => now(),
            'admin_notes' => $this->faker->optional()->text(100),
        ]);
    }
}
