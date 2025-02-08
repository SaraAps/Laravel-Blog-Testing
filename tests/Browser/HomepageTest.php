<?php

namespace Tests\Browser;
use App\Models\Category;
use App\Models\Post;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomepageTest extends DuskTestCase
{
    public function testHomepageDisplaysPosts()
    {
        $posts = Post::factory()->create();

        $this->browse(function (Browser $browser) use ($posts) {
            $browser->visit('/')
                ->assertSee($posts->first()->title)
                ->clickLink($posts->first()->title)
                ->assertPathBeginsWith('/posts/')
                ->assertSee("Back to Posts");
        });
    }

    public function testHomepageCategoryDropdown()
    {
        $categories = Category::factory()->count(2)->create();
        $posts = collect();

        foreach ($categories as $category) {
            $posts->push(Post::factory()->for($category)->create());
        }

        $this->browse(function (Browser $browser) use ($posts, $categories) {
            $browser->visit('/')
                ->assertSee($posts->first()->title)
                ->press("Categories");

            foreach ($categories as $category) {
                $browser->pause(1000)
                    ->assertSee($category->name);
            }

            $selectedCategory = $categories->first();
            $browser->clickLink($selectedCategory->name)
                ->pause(1000);
        });
    }


    public function testSearchField()
    {
        $post = Post::factory()->create();
        $searchQuery = substr($post->title, 0, 5);

        $this->browse(function (Browser $browser) use ($post, $searchQuery) {
            $browser->visit('/')
                ->waitFor('input[name="search"]', 5)
                ->type('input[name="search"]', $searchQuery)
                ->keys('input[name="search"]', '{enter}')
                ->pause(1500)
                ->assertSee($post->title);
        });
    }



    public function testFailSearchField()
    {

        $this->browse(function (Browser $browser){
            $browser->visit('/')
                ->waitFor('input[name="search"]', 5)
                ->type('input[name="search"]', "random")
                ->keys('input[name="search"]', '{enter}')
                ->pause(1500)
                ->assertSee("No posts yet.");
        });
    }

}
