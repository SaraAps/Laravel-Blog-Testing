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
        $app = $this->app;
        $trustHosts = new TrustHosts($app);

        // Set the application URL to a domain with subdomains
        config(['app.url' => 'https://example.com']);

        // Get the trusted hosts
        $trustedHosts = $trustHosts->hosts();

        // Assert that all subdomains of the application URL are trusted
        $this->assertContains('^(.+)\.example\.com$', $trustedHosts);
    }
    /** @test */
    public function it_redirects_unauthenticated_users_to_login_for_non_json_requests()
    {
        // Create a fake route that uses the Authenticate middleware
        Route::get('/protected', function () {
            return 'Protected content';
        })->middleware(Authenticate::class);

        // Make a request to the protected route WITHOUT authentication
        $response = $this->get('/protected');

        // Assert redirect to login route (non-JSON requests)
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_unauthorized_response_for_json_requests()
    {
        // Create a fake route that uses the Authenticate middleware
        Route::get('/protected', function () {
            return 'Protected content';
        })->middleware(Authenticate::class);

        // Make a JSON request (e.g., API call) WITHOUT authentication
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/protected');

        // Assert 401 Unauthorized response (JSON requests)
        $response->assertStatus(401);
    }

    /** @test */
    public function it_redirects_authenticated_users_to_home_route()
    {
        // Define a fake route that uses the RedirectIfAuthenticated middleware
        Route::get('/guest-route', function () {
            return 'Guest content';
        })->middleware(RedirectIfAuthenticated::class);

        // Create and authenticate a user
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Make a request to the guest route while authenticated
        $response = $this->get('/guest-route');

        // Assert that the user is redirected to the HOME route
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
