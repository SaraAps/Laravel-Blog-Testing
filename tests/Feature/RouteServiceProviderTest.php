<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Tests\TestCase;
use App\Models\User;

class RouteServiceProviderTest extends TestCase
{
    public function test_boot_configures_routes()
    {
        // Mock the Route facade for the API route group
        Route::shouldReceive('prefix')->with('api')->once()->andReturnSelf();
        Route::shouldReceive('middleware')->with('api')->once()->andReturnSelf();
        Route::shouldReceive('namespace')->with($this->app->getNamespace())->once()->andReturnSelf();
        Route::shouldReceive('group')->with(base_path('routes/api.php'))->once();

        // Mock the Route facade for the web route group
        Route::shouldReceive('middleware')->with('web')->once()->andReturnSelf();
        Route::shouldReceive('namespace')->with($this->app->getNamespace())->once()->andReturnSelf();
        Route::shouldReceive('group')->with(base_path('routes/web.php'))->once();

        // Create an instance of the RouteServiceProvider
        $provider = new RouteServiceProvider($this->app);

        // Call the boot method
        $provider->boot();
    }


    public function test_rate_limiting()
    {
        RateLimiter::shouldReceive('for')->once();

        $provider = new RouteServiceProvider(App());
        $provider->boot();
    }
    /** @test */
    public function test_api_rate_limiting()
    {
        // Create a user
        $user = User::factory()->create();

        // Make 60 requests as an authenticated user
        for ($i = 0; $i < 60; $i++) {
            $response = $this->actingAs($user, 'api')->get('/api/user');
            $response->assertStatus(200); // All requests should succeed
        }

        // The 61st request should fail due to rate limiting
        $response = $this->actingAs($user, 'api')->get('/api/user');
        $response->assertStatus(429); // Too Many Requests
    }
}
