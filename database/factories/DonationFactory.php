<?php

namespace Database\Factories;

use App\Models\BloodRequest;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'blood_request_id' => BloodRequest::factory(),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['cart', 'completed']),
            'completed_at' => now(),
        ];
    }
}
