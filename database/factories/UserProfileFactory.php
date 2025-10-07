<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'bio' => $this->faker->text(200),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'rating' => $this->faker->randomFloat(2, 3.0, 5.0),
            'reputation_points' => 0,
            'badge_counts' => null,
            'last_badge_check' => null,
        ];
    }
}
