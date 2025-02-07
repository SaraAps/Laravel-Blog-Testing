<?php
namespace Tests\Browser;

    use App\Models\Category;
    use App\Models\Post;
    use App\Models\User;
    use Laravel\Dusk\Browser;
    use Tests\DuskTestCase;

class AdminEditPostTest extends DuskTestCase
{
    public function testAdminCanEditPost()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::factory()->create();
        $newCategory = Category::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $post, $newCategory) {
            $browser->loginAs($admin)
                ->visit("/admin/posts/{$post->id}/edit")
                ->assertInputValue('title', $post->title) // Check existing title
                ->type('title', 'Updated Post Title')
                ->type('slug', 'updated-post-title')
                ->attach('thumbnail', 'public/images/illustration-3.png')
                ->type('excerpt', 'Updated excerpt content.')
                ->type('body', 'Updated post body content.')
                ->select('category_id', $newCategory->id)
                ->scrollTo('')
                ->script("document.querySelector('button[type=\"submit\"]').click();");

            // Continue with Dusk chain
            $browser->pause(5000)
                ->assertPathIs("/admin/posts/{$post->id}/edit")
                ->assertSee('Updated');
        });
    }

    public function testEditPostShowsValidationErrors()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $post) {
            $browser->loginAs($admin)
                ->visit("/admin/posts/{$post->id}/edit")
                ->scrollTo('')
                ->type('title', '') // Clear the title field
                ->script("document.querySelector('button[type=\"submit\"]').click();");

            $browser->pause(5000)
                ->assertScript('return document.querySelector("input[name=\'title\']").validity.valueMissing === true');
        });
    }
}
