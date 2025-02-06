<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminPostTest extends DuskTestCase
{
    public function testAdminCanCreatePost()
    {
        $admin = User::factory()->create(['is_admin' => true]); // Adjust to your admin role logic
        $category = Category::factory()->create();

        $this->browse(function (Browser $browser) use ($category, $admin) {
            $browser->loginAs($admin)
                ->visit('/admin/posts/create')
                ->type('title', 'New Post Title')
                ->type('slug', 'new-post-title')
                ->attach('thumbnail', 'public/images/illustration-3.png') // Ensure this path is correct
                ->type('excerpt', 'This is the content of the new post.')
                ->type('body', 'This is the content of the new post, and the body.')
                ->select('category_id', $category->id)
                ->scrollTo('')
                ->script("document.querySelector('button[type=\"submit\"]').click();"); // Click the button using JavaScript

            // Continue with the Dusk chain
            $browser->pause(5000) // Pause to observe the result
            ->assertPathIs('/') // Adjust to your expected post-creation path
            ->assertSee('New Post Title'); // Confirm the post was created
        });
    }
}
