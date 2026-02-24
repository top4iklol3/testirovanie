<?php
// database/factories/PollFactory.php

namespace Database\Factories;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollFactory extends Factory
{
    protected $model = Poll::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'is_active' => $this->faker->boolean(80),
            'allow_multiple_votes' => $this->faker->boolean(30),
            'ends_at' => $this->faker->optional(0.5)->dateTimeBetween('+1 day', '+1 month'),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
