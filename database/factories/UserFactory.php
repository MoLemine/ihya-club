<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->numerify('06########'),
            'city' => fake()->randomElement(['Nouakchott', 'Nouadhibou', 'Rosso', 'Atar']),
            'bio' => fake()->optional()->sentence(10),
            'preferred_locale' => fake()->randomElement(['ar', 'fr']),
            'role' => 'user',
            'is_guest' => false,
            'is_suspended' => false,
            'posting_restricted' => false,
            'profile_locked' => false,
            'last_donation_date' => fake()->optional()->dateTimeBetween('-8 months', '-4 months'),
            'points_balance' => 0,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
