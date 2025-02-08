<?php

namespace Tests\Browser;

use App\Models\Post;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminDeletePostTest extends DuskTestCase
{
    public function testDeletePost()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::factory()->create(['title'=>'Post to delete']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/posts')
                ->pause(1000)
                ->assertSee('Delete')
                ->pause(1000)
                ->within('.min-w-full tr:last-child', function ($browser) {
                    $browser->press('Delete');
                })
                ->assertDontSee('Post to delete');
        });
    }

}
