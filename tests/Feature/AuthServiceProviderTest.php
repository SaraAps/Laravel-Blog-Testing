<?php

namespace Tests\Feature\Providers;


use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AuthServiceProviderTest extends TestCase
{
    /** @test */
    public function it_registers_the_admin_gate()
    {
// Create a non-admin user
        $nonAdminUser = User::factory()->create(['is_admin' => false]);

// Create an admin user
        $adminUser = User::factory()->create(['is_admin' => true]);

// Assert that the admin gate works as expected
        $this->assertFalse(Gate::forUser($nonAdminUser)->allows('admin'));
        $this->assertTrue(Gate::forUser($adminUser)->allows('admin'));
    }
}
