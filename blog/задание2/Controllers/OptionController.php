<?php
// database/factories/OptionFactory.php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory
{
    protected $model = Option::class;

    public function definition()
    {
        return [
            'poll_id' => Poll::factory(),
            'text' => $this->faker->sentence(3),
            'votes_count' => $this->faker->numberBetween(0, 100)
        ];
    }
}
