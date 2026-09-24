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
}
