<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrustHosts;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthMiddlewareTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function it_allows_authenticated_users_to_access_protected_routes()
    {
        // Create a fake route that uses the Authenticate middleware
        Route::get('/protected', function () {
            return 'Protected content';
        })->middleware(Authenticate::class);

        // Authenticate a user
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Make a request to the protected route
        $response = $this->get('/protected');

        // Assert that the user can access the protected content
        $response->assertStatus(200);
        $response->assertSee('Protected content');
    }


    /** @test */
    public function it_allows_guest_users_to_access_guest_routes()
    {
        // Create a fake route that uses the RedirectIfAuthenticated middleware
        Route::get('/guest', function () {
            return 'Guest content';
        })->middleware(RedirectIfAuthenticated::class);

        // Make a request to the guest route
        $response = $this->get('/guest');

        // Assert that the guest user can access the guest content
        $response->assertStatus(200);
        $response->assertSee('Guest content');
    }

    /** @test */
    public function it_trusts_all_subdomains_of_the_application_url()
    {
        // Create an instance of the TrustHosts middleware
        $trustHosts = new TrustHosts();

        // Set the application URL to a domain with subdomains
        config(['app.url' => 'https://example.com']);

        // Get the trusted hosts
        $trustedHosts = $trustHosts->hosts();

        // Assert that all subdomains of the application URL are trusted
        $this->assertContains('^(.+)\.example\.com$', $trustedHosts);
    }
}
