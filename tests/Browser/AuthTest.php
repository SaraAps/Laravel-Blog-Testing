<?php

namespace Tests\Browser;

    use App\Models\User;
    use Tests\DuskTestCase;
    use Laravel\Dusk\Browser;
    use Faker\Factory;

class AuthTest extends DuskTestCase
{
    public function testUserCanRegisterAndLogin()
    {
        $faker = Factory::create();

        $this->browse(function (Browser $browser) use ($faker) {
            $name = $faker->name;
            $username = $faker->unique()->userName;
            $email = $faker->unique()->safeEmail;
            $password = 'Password123@';

            $browser->visit('/register')
                ->type('name', $name)
                ->type('username', $username)
                ->type('email', $email)
                ->type('password', $password)
                ->pause(2000)
                ->press('SIGN UP')
                ->assertPathIs('/')
                ->assertSee('Laravel');

            $browser->press('WELCOME, ' . strtoupper($name) . '!')
            ->clickLink('Log Out')
                ->pause(1000)
                ->assertPathIs('/')
                ->assertSee('Laravel');

            $browser->visit('/login')
                ->type('email', $email)
                ->type('password', $password)
                ->pause(1000)
                ->press('LOG IN')
                ->assertPathIs('/')
                ->assertSee('Laravel');
        });
    }

}
