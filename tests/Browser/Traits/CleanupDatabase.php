<?php
namespace Tests\Browser\Traits;

use Illuminate\Support\Facades\Artisan;

trait CleanupDatabase
{
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh');
    }

    protected function tearDown(): void
    {
        // Additional cleanup if needed
        parent::tearDown();
    }
}
