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
            'preferred_datetime' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'quoted_price' => $this->faker->randomFloat(2, 50, 500),
            'client_notes' => $this->faker->optional()->text(200),
            'client_address' => [
                'street' => $this->faker->streetAddress,
                'city' => $this->faker->city,
                'postal_code' => $this->faker->postcode,
            ],
        ];
    }
}
