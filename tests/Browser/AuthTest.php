<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
class AuthTest extends DuskTestCase
{
    public function testUserCanRegisterAndLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('name', 'Test User')
                ->type('username', 'testuser1234567')
                ->type('email', 'testuser1234@example.com')
                ->type('password', 'Password123@')
                ->press('SIGN UP')
                ->assertPathIs('/')
                ->assertSee('Laravel');

            $browser->press('WELCOME, TEST USER!')
                ->clickLink('Log Out')
                ->assertPathIs('/')
                ->assertSee('Laravel');

            $browser->visit('/login')
                ->type('email', 'testuser123@example.com')
                ->type('password', 'Password123@')
                ->press('LOG IN')
                ->assertPathIs('/')
                ->assertSee('Laravel');
        });
    }


}
