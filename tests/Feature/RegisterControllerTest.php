<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_the_registration_form()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('register.create');
    }

    /** @test */
    public function it_registers_a_new_user()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password123@',
            'password_confirmation' => 'Password123@',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function it_fails_to_register_with_invalid_data()
    {
        $response = $this->post('/register', [
            'name' => '',
            'username' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors(['name', 'username', 'email', 'password']);
        $this->assertGuest();
    }
}
