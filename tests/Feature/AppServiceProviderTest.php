<?php

namespace Tests\Unit\Providers;

use App\Providers\AppServiceProvider;
use App\Services\Newsletter;
use App\Services\MailchimpNewsletter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_newsletter_binding()
    {
        $provider = new AppServiceProvider(App());
        $provider->register();

        $this->assertInstanceOf(MailchimpNewsletter::class, app(Newsletter::class));
    }

    public function test_admin_gate()
    {
        $provider = new AppServiceProvider(App());
        $provider->boot();

        $user = new \App\Models\User(['username' => 'JeffreyWay']);
        $this->assertTrue(Gate::forUser($user)->allows('admin'));
    }

}
