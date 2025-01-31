<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    public function test_guest_cannot_access_admin_dashboard()
    {
        $response = $this->get('/admin/posts');
        $response->assertStatus(403); // Forbidden
    }

    public function test_non_admin_cannot_access_admin_dashboard()
    {
        $user = \App\Models\User::factory()->create(['is_admin' => false]);
        $response = $this->actingAs($user)->get('/admin/posts');
        $response->assertStatus(403); // Forbidden
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        $response = $this->actingAs($admin)->get('/admin/posts');
        $response->assertStatus(200);
    }
}
