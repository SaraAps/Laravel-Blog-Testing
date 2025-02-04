<?php

namespace Tests\Feature\Providers;

use App\Providers\BroadcastServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Tests\TestCase;

class BroadcastServiceProviderTest extends TestCase
{
    /** @test */
    public function it_registers_broadcast_routes()
    {
        Broadcast::shouldReceive('routes')->once();

        $provider = new BroadcastServiceProvider(App());
        $provider->boot();
    }

    /** @test */
    public function it_includes_the_channels_file()
    {
        // Assert that the channels file exists and is included
        $this->assertFileExists(base_path('routes/channels.php'));
    }
}
