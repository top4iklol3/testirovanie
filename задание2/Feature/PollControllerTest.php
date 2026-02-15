<?php


namespace Tests\Feature;

use App\Models\Poll;
use App\Models\Option;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PollControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_poll_with_options()
    {
        $pollData = [
            'title' => 'Тестовый опрос',
            'description' => 'Описание тестового опроса',
            'options' => ['Вариант 1', 'Вариант 2', 'Вариант 3'],
            'allow_multiple_votes' => false
        ];


        $response = $this->postJson('/api/polls', $pollData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Опрос успешно создан'
            ]);

        $this->assertDatabaseHas('polls', [
            'title' => 'Тестовый опрос',
            'allow_multiple_votes' => false
        ]);

        $poll = Poll::where('title', 'Тестовый опрос')->first();
        $this->assertEquals(3, $poll->options()->count());
    }

    /** @test */
    public function it_validates_poll_creation_data()
    {
        $invalidData = [
            'title' => '',
            'options' => ['Только один вариант']
        ];


        $response = $this->postJson('/api/polls', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'options']);
    }

    /** @test */
    public function it_can_list_all_polls()
    {
        Poll::factory()->count(3)->create();


        $response = $this->getJson('/api/polls');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_show_a_single_poll()
    {
        $poll = Poll::factory()->create();
        Option::factory()->count(3)->create(['poll_id' => $poll->id]);


        $response = $this->getJson("/api/polls/{$poll->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $poll->id,
                    'title' => $poll->title
                ]
            ]);
    }

    /** @test */
    public function it_can_delete_a_poll()
    {
        $poll = Poll::factory()->create();
        Option::factory()->count(3)->create(['poll_id' => $poll->id]);


        $response = $this->deleteJson("/api/polls/{$poll->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Опрос успешно удален'
            ]);

        $this->assertDatabaseMissing('polls', ['id' => $poll->id]);
        $this->assertDatabaseMissing('options', ['poll_id' => $poll->id]);
    }
}
