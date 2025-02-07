<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminCreatePostTest extends DuskTestCase
{
    public function testAdminCanCreatePost()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create();

        $this->browse(function (Browser $browser) use ($category, $admin) {
            $browser->loginAs($admin)
                ->visit('/admin/posts/create')
                ->type('title', 'New Post Title')
                ->type('slug', 'new-post-title')
                ->attach('thumbnail', 'public/images/illustration-3.png')
                ->type('excerpt', 'This is the content of the new post.')
                ->type('body', 'This is the content of the new post, and the body.')
                ->select('category_id', $category->id)
                ->scrollTo('')
                ->script("document.querySelector('button[type=\"submit\"]').click();");

            // Continue with the Dusk chain
            $browser->pause(5000)
            ->assertPathIs('/')
            ->assertSee('New Post Title');
        });
    }
    public function testPostCreationShowsValidationErrors()
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/admin/posts/create')
                ->scrollTo('')
                ->script("document.querySelector('button[type=\"submit\"]').click();");

            $browser->pause(5000)
                ->assertScript('return document.querySelector("input[name=\'title\']").validity.valueMissing === true');
        });
    }
}
