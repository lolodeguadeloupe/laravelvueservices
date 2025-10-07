<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingRequest>
 */
class BookingRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => User::factory(),
            'provider_id' => User::factory(),
            'service_id' => Service::factory(),
            'status' => 'pending',
            'scheduled_date' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'price' => $this->faker->randomFloat(2, 50, 500),
            'notes' => $this->faker->optional()->text(200),
        ];
    }
}
