<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a verified user can create a post.
     *
     * @return void
     */
    public function test_verified_user_can_create_post()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'title' => 'Test Post',
                'content' => 'This is a test post',
                'status' => 'draft',
                'allow_comments' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Post created successfully',
            ]);

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'title' => 'Test Post',
            'content' => 'This is a test post',
            'status' => 'draft',
            'allow_comments' => 1,
        ]);
    }

    /**
     * Test that an unverified user cannot create a post.
     *
     * @return void
     */
    public function test_unverified_user_cannot_create_post()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'title' => 'Test Post',
                'content' => 'This is a test post',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('posts', [
            'user_id' => $user->id,
            'title' => 'Test Post',
        ]);
    }

    /**
     * Test that a guest cannot create a post.
     *
     * @return void
     */
    public function test_guest_cannot_create_post()
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test post validation rules.
     *
     * @return void
     */
    public function test_post_validation_rules()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'title' => str_repeat('a', 51), // 51 characters
                'content' => 'This is a test post',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'title' => 'Test Post',
                'content' => str_repeat('a', 2001), // 2001 characters
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['content']);

        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'title' => 'Test Post',
                'content' => 'This is a test post',
                'status' => 'invalid-status',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'title' => '',
                'content' => 'This is a test post',
                'status' => 'published',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test default values for post.
     *
     * @return void
     */
    public function test_post_default_values()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/posts', [
                'title' => 'Test Post',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'title' => 'Test Post',
            'status' => 'draft',
            'allow_comments' => 1,
        ]);
    }
}
