<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_register_temporarily(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => [
                         'id',
                         'name',
                         'email',
                         'created_at',
                         'updated_at',
                     ],
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user->verification_token);
        $this->assertNull($user->email_verified_at);
    }

    public function test_users_can_verify_email(): void
    {
        $user = User::factory()->create([
            'verification_token' => 'test-token',
            'email_verified_at' => null,
        ]);

        $response = $this->getJson('/api/auth/verify/test-token');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'user',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'verification_token' => null,
        ]);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_invalid_verification_token(): void
    {
        $response = $this->getJson('/api/auth/verify/invalid-token');

        $response->assertStatus(400)
                 ->assertJson([
                     'message' => 'Invalid verification token',
                 ]);
    }
}
