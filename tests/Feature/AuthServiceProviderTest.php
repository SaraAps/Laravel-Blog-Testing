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
        $nonAdminUser = User::factory()->create(['is_admin' => false]);

        $adminUser = User::factory()->create(['is_admin' => true]);

        $this->assertFalse(Gate::forUser($nonAdminUser)->allows('admin'));
        $this->assertTrue(Gate::forUser($adminUser)->allows('admin'));
    }
}
