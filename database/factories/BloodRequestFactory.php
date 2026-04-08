<?php

namespace Database\Factories;

use App\Models\BloodRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BloodRequestFactory extends Factory
{
    protected $model = BloodRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'patient_name' => fake()->optional()->name(),
            'hospital_name' => fake()->randomElement(['Centre Hospitalier National', 'Hopital Cheikh Zayed', 'Clinique El Amal']),
            'city' => fake()->randomElement(['Nouakchott', 'Nouadhibou', 'Rosso', 'Atar']),
            'urgency_level' => fake()->randomElement(['normal', 'urgent']),
            'required_units' => fake()->numberBetween(1, 5),
            'fulfilled_units' => 0,
            'description' => fake()->paragraph(),
            'blood_type' => 'O-',
            'status' => fake()->randomElement(['pending', 'approved']),
            'approved_at' => now(),
            'expires_at' => now()->addDays(7),
        ];
    }
}
