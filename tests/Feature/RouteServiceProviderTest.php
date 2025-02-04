<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Tests\TestCase;

class RouteServiceProviderTest extends TestCase
{
    public function test_boot_configures_routes()
    {
        Route::shouldReceive('prefix')->with('api')->andReturnSelf();
        Route::shouldReceive('middleware')->with('api')->andReturnSelf();
        Route::shouldReceive('group')->once();

        Route::shouldReceive('middleware')->with('web')->andReturnSelf();
        Route::shouldReceive('group')->once();

        $provider = new RouteServiceProvider(app());
        $provider->boot();
    }

    public function test_rate_limiting()
    {
        RateLimiter::shouldReceive('for')->once();

        $provider = new RouteServiceProvider(App());
        $provider->boot();
    }
    public function test_api_rate_limiting()
    {
        // Simulate a request from a test user
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $request = Request::create('/api/test', 'GET');

        // Apply the rate limiter logic
        $key = optional($user)->id ?: $request->ip();
        RateLimiter::hit('api:' . $key);

        // Ensure the request is being limited after 60 hits
        for ($i = 0; $i < 60; $i++) {
            RateLimiter::hit('api:' . $key);
        }

        $this->assertTrue(RateLimiter::tooManyAttempts('api:' . $key, 60));
    }
}
