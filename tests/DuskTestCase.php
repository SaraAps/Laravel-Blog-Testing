<?php

namespace Tests;

use Dotenv\Dotenv;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Facades\Artisan;
use Laravel\Dusk\TestCase as BaseTestCase;
use Tests\Browser\Traits\CleanupDatabase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;
    use CleanupDatabase;
    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // Load .env.dusk.local specifically for Dusk
        $dotenv = Dotenv::createImmutable(base_path(), '.env.dusk.local');
        $dotenv->load();

        // Optionally, run any database migrations if necessary
        Artisan::call('migrate');
    }
    public static function prepare()
    {
        static::startChromeDriver();

    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
        ])->unless($this->hasHeadlessDisabled(), function ($items) {
            return $items->merge([
                '--disable-gpu',
                '--headless',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:65511',
            DesiredCapabilities::chrome()
        );


    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     *
     * @return bool
     */
    protected function hasHeadlessDisabled()
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
               isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }

    /**
     * Determine if the browser window should start maximized.
     *
     * @return bool
     */
    protected function shouldStartMaximized()
    {
        return isset($_SERVER['DUSK_START_MAXIMIZED']) ||
               isset($_ENV['DUSK_START_MAXIMIZED']);
    }
}
