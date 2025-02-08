<?php

namespace Tests\Browser;

use App\Models\Post;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PostCommentsTest extends DuskTestCase
{
    public function testUserCanCommentOnPost()
    {
        $posts = Post::factory()->create();
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user, $posts) {
            $browser->loginAs($user)
                ->visit('/')
                ->assertSee($posts->first()->title)
                ->clickLink($posts->first()->title)
                ->assertPathBeginsWith('/posts/')
                ->scrollTo('textarea[name="body"]')
                ->type('textarea[name="body"]', 'This is a comment.')
                ->pause(3000)
                ->scrollTo('button[type="submit"]')
                ->pause(1000)
                ->waitFor('button[type="submit"]')
                ->pause(1000)
                ->click('button[type="submit"]')
                ->assertSee('This is a comment.');
        });
    }

    public function testAdminCanCommentOnPost()
    {
        $posts = Post::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->browse(function (Browser $browser) use ($admin, $posts) {
            $browser->loginAs($admin)
                ->visit('/')
                ->assertSee($posts->first()->title)
                ->clickLink($posts->first()->title)
                ->assertPathBeginsWith('/posts/')
                ->scrollTo('textarea[name="body"]')
                ->type('textarea[name="body"]', 'This is an ADMIN comment.')
                ->pause(3000)
                ->scrollTo('button[type="submit"]')
                ->pause(1000)
                ->waitFor('button[type="submit"]')
                ->pause(1000)
                ->click('button[type="submit"]')
                ->assertSee('This is an ADMIN comment.');
        });
    }

    public function testInvalidCommentOnPost()
    {
        $posts = Post::factory()->create();
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user, $posts) {
            $browser->loginAs($user)
                ->visit('/')
                ->assertSee($posts->first()->title)
                ->clickLink($posts->first()->title)
                ->assertPathBeginsWith('/posts/')
                ->scrollTo('textarea[name="body"]')
                ->type('textarea[name="body"]', '')
                ->pause(3000)
                ->scrollTo('button[type="submit"]')
                ->waitFor('button[type="submit"]')
                ->press('button[type="submit"]')
                ->assertScript('return document.querySelector("textarea[name=\'body\']").validity.valueMissing === true');
        });
    }
}
