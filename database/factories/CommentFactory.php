<?php

namespace Database\Factories;

use App\Models\BloodRequest;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'blood_request_id' => BloodRequest::factory(),
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'content' => fake()->sentence(12),
        ];
    }
}
