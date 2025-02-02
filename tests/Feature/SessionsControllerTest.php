<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SessionsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_the_login_form()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('sessions.create');
    }

    /** @test */
    public function it_logs_in_a_user_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('Password123@'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password123@',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);


        $this->assertNotNull(session()->get('_token'));
        $response->assertSessionHas('success', 'Welcome Back!');
    }

    /** @test */
    public function it_fails_to_log_in_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('Password123@'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function it_logs_out_a_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();

        // Test flash message
        $response->assertSessionHas('success', 'Goodbye!');
    }
}
