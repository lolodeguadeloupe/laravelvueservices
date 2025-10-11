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
            'provider_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->text(200),
            'price' => $this->faker->randomFloat(2, 20, 500),
            'price_type' => $this->faker->randomElement(['fixed', 'hourly']),
            'duration' => $this->faker->randomElement([30, 60, 90, 120, 180]),
            'is_active' => true,
        ];
    }
}
