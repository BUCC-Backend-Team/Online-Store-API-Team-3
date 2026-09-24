<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use App\Models\User;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Define a dummy route protected by the middleware for testing
        Route::middleware(['auth:api', 'role:ADMIN'])->get('/api/v1/test-admin-route', function () {
            return response()->json(['success' => true]);
        });
    }

    public function test_admin_can_access_admin_route(): void
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        $token = auth('api')->login($admin);

        $response = $this->withToken($token)->getJson('/api/v1/test-admin-route');
        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_admin_route(): void
    {
        $customer = User::factory()->create(['role' => 'CUSTOMER']);
        $token = auth('api')->login($customer);

        $response = $this->withToken($token)->getJson('/api/v1/test-admin-route');
        
        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Forbidden'
                 ]);
    }
    
    public function test_unauthenticated_user_cannot_access_admin_route(): void
    {
        $response = $this->getJson('/api/v1/test-admin-route');
        $response->assertStatus(401);
    }
}
