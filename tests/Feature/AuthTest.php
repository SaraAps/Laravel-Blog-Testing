<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123@',
            'password_confirmation' => 'Password123@'
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_admin_login()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'Password123@'
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
