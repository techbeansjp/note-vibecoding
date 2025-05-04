<?php

namespace Tests\Feature;

use App\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration.
     */
    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'status' => 'provisional',
        ]);

        $this->assertDatabaseCount('verification_tokens', 1);
    }

    /**
     * Test email verification.
     */
    public function test_user_can_verify_email(): void
    {
        $user = User::factory()->create([
            'status' => 'provisional',
            'email_verified_at' => null,
        ]);

        $token = 'test-token-' . uniqid();
        DB::table('verification_tokens')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson("/api/verify/{$token}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Email verified successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseMissing('verification_tokens', [
            'token' => $token,
        ]);
    }

    /**
     * Test user login.
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'expires_in',
                'user',
            ]);
    }

    /**
     * Test login with invalid credentials.
     */
    public function test_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    /**
     * Test verification with invalid token.
     */
    public function test_verification_with_invalid_token(): void
    {
        $response = $this->getJson('/api/verify/invalid-token');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid or expired verification token',
            ]);
    }

    /**
     * Test registration with invalid data.
     */
    public function test_registration_with_invalid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
