<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;
use PHPUnit\Framework\Attributes\Test;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function it_displays_the_list_of_tasks(): void
    {
        Task::factory(3)->create();
        $response = $this->get(route('tasks.index'));
        $response->assertOk();
        $response->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 3;
        });
    }

    #[Test]
    public function it_can_create_a_new_task(): void
    {
        $taskData = ['title' => 'Новая задача'];
        $this->post(route('tasks.store'), $taskData)
            ->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', $taskData);
    }

    #[Test]
    public function it_can_update_a_task(): void
    {
        $task = Task::factory()->create();
        $updatedData = ['title' => 'Обновленная задача'];
        $this->put(route('tasks.update', $task), $updatedData)
            ->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Обновленная задача',
        ]);
    }

    #[Test]
    public function it_can_delete_a_task(): void
    {
        $task = Task::factory()->create();
        $this->delete(route('tasks.destroy', $task))
            ->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function it_can_mark_a_task_as_completed(): void
    {
        $task = Task::factory()->create(['completed' => false]);
        $this->patch(route('tasks.complete', $task))
            ->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => true,
        ]);
    }

    #[Test]
    public function it_can_mark_a_task_as_not_completed(): void
    {
        $task = Task::factory()->create(['completed' => true]);
        $this->patch(route('tasks.complete', $task))
            ->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed' => false,
        ]);
    }

    #[Test]
    public function a_title_is_required_to_create_a_task(): void
    {
        $response = $this->post(route('tasks.store'), ['title' => '']);
        $response->assertSessionHasErrors('title');
    }

    #[Test]
    public function a_title_is_required_to_update_a_task(): void
    {
        $task = Task::factory()->create();
        $response = $this->put(route('tasks.update', $task), ['title' => '']);
        $response->assertSessionHasErrors('title');
    }
}
