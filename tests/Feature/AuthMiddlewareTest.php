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
        Route::get('/protected', function () {
            return 'Protected content';
        })->middleware(Authenticate::class);

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/protected');

        $response->assertStatus(200);
        $response->assertSee('Protected content');
    }


    /** @test */
    public function it_allows_guest_users_to_access_guest_routes()
    {
        Route::get('/guest', function () {
            return 'Guest content';
        })->middleware(RedirectIfAuthenticated::class);

        $response = $this->get('/guest');

        $response->assertStatus(200);
        $response->assertSee('Guest content');
    }

    /** @test */
    public function it_trusts_all_subdomains_of_the_application_url()
    {
        $app = $this->app;
        $trustHosts = new TrustHosts($app);

        config(['app.url' => 'http://127.0.0.1:8000']);
        $trustedHosts = $trustHosts->hosts();

        $this->assertContains('^127\.0\.0\.1$', $trustedHosts);
    }
    /** @test */
    public function it_redirects_unauthenticated_users_to_login_for_non_json_requests()
    {
        Route::get('/protected', function () {
            return 'Protected content';
        })->middleware(Authenticate::class);

        // Make a request to the protected route WITHOUT authentication
        $response = $this->get('/protected');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_unauthorized_response_for_json_requests()
    {
        Route::get('/protected', function () {
            return 'Protected content';
        })->middleware(Authenticate::class);

        // Make a JSON request WITHOUT authentication
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/protected');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_redirects_authenticated_users_to_home_route()
    {
        Route::get('/guest-route', function () {
            return 'Guest content';
        })->middleware(RedirectIfAuthenticated::class);

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/guest-route');

        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
