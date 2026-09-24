<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test Registration',
            'email' => 'newuser@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'token',
                     'user' => ['id', 'name', 'email', 'role']
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'role' => 'CUSTOMER',
        ]);
    }

    public function test_registration_validation_format(): void
    {
        // This tests our custom 400 Bad Request standard from Section 10
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Validation failed',
                 ])
                 ->assertJsonStructure([
                     'errors' => ['name', 'email', 'password']
                 ]);
    }

    public function test_user_can_login(): void
    {
        $user = \App\Models\User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'token']);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = \App\Models\User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Invalid credentials'
                 ]);
    }

    public function test_user_can_get_their_profile(): void
    {
        $user = \App\Models\User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withToken($token)->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'user' => [
                         'id' => $user->id,
                         'email' => $user->email,
                     ]
                 ]);
    }

    public function test_user_can_logout(): void
    {
        $user = \App\Models\User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withToken($token)->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Successfully logged out'
                 ]);
    }
}
