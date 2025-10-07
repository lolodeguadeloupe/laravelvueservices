<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->text(200),
            'price_min' => $this->faker->randomFloat(2, 20, 100),
            'price_max' => $this->faker->randomFloat(2, 100, 500),
            'duration' => $this->faker->randomElement([30, 60, 90, 120, 180]),
            'is_active' => true,
        ];
    }
}
