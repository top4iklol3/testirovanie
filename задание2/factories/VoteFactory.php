<?php
// database/factories/VoteFactory.php

namespace Database\Factories;

use App\Models\Vote;
use App\Models\Poll;
use App\Models\Option;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoteFactory extends Factory
{
    protected $model = Vote::class;

    public function definition(): array
    {
        $poll = Poll::factory()->create();

        return [
            'poll_id' => $poll->id,
            'option_id' => Option::factory()->create(['poll_id' => $poll->id]),
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
