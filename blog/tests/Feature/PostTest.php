<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;

class PostTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that a guest cannot create a post.
     *
     * @return void
     */
    public function test_guest_cannot_create_post()
    {
        $postData = [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'categories' => [Category::factory()->create()->id],
        ];

        $this->post(route('posts.store'), $postData)
            ->assertRedirect(route('login'));

        $this->assertDatabaseCount('posts', 0);
    }

    /**
     * Test that an authenticated user can create a post.
     *
     * @return void
     */
    public function test_authenticated_user_can_create_post()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $postData = [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'categories' => [$category->id],
        ];

        $this->actingAs($user)
            ->post(route('posts.store'), $postData)
            ->assertRedirect(route('posts.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'title' => $postData['title'],
            'body' => $postData['body'],
            'user_id' => $user->id,
        ]);

        $post = Post::first();
        $this->assertTrue($post->categories->contains($category));
    }

    /**
     * Test that a user cannot update another user's post.
     *
     * @return void
     */
    public function test_user_cannot_update_another_users_post()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($owner)->create();
        $category = Category::factory()->create();

        $updatedData = [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
            'categories' => [$category->id],
        ];

        $this->actingAs($otherUser)
            ->put(route('posts.update', $post), $updatedData)
            ->assertForbidden(); // Expect a 403 Forbidden response

        $this->assertDatabaseMissing('posts', [
            'title' => $updatedData['title'],
            'body' => $updatedData['body'],
        ]);
    }

    /**
     * Test that a user can update their own post.
     *
     * @return void
     */
    public function test_user_can_update_their_own_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();
        $category = Category::factory()->create();

        $updatedData = [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
            'categories' => [$category->id],
        ];

        $this->actingAs($user)
            ->put(route('posts.update', $post), $updatedData)
            ->assertRedirect(route('posts.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $updatedData['title'],
            'body' => $updatedData['body'],
        ]);

        $post->refresh();
        $this->assertTrue($post->categories->contains($category));
    }

    /**
     * Test that a user cannot delete another user's post.
     *
     * @return void
     */
    public function test_user_cannot_delete_another_users_post()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($owner)->create();

        $this->actingAs($otherUser)
            ->delete(route('posts.destroy', $post))
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id]); // Post should still exist
    }

    /**
     * Test that a user can delete their own post.
     *
     * @return void
     */
    public function test_user_can_delete_their_own_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('posts.destroy', $post))
            ->assertRedirect(route('posts.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('posts', ['id' => $post->id]); // Post should be deleted
    }

    /**
     * Test that a guest cannot view the create post page.
     *
     * @return void
     */
    public function test_guest_cannot_view_create_post_page()
    {
        $this->get(route('posts.create'))
            ->assertRedirect(route('login'));
    }

    /**
     * Test that an authenticated user can view the create post page.
     *
     * @return void
     */
    public function test_authenticated_user_can_view_create_post_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('posts.create'))
            ->assertOk();
    }

    /**
     * Test that a guest cannot view the edit post page.
     *
     * @return void
     */
    public function test_guest_cannot_view_edit_post_page()
    {
        $post = Post::factory()->create();
        $this->get(route('posts.edit', $post))
            ->assertRedirect(route('login'));
    }

    /**
     * Test that a user can view their own edit post page.
     *
     * @return void
     */
    public function test_user_can_view_their_own_edit_post_page()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $this->actingAs($user)
            ->get(route('posts.edit', $post))
            ->assertOk();
    }

    /**
     * Test that a user cannot view another user's edit post page.
     *
     * @return void
     */
    public function test_user_cannot_view_another_users_edit_post_page()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->for($owner)->create();

        $this->actingAs($otherUser)
            ->get(route('posts.edit', $post))
            ->assertForbidden();
    }
}
