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
        $posts = Post::factory()->count(3)->create();

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
        $categories = Category::factory()->count(3)->create();
        $posts = collect();

        foreach ($categories as $category) {
            $posts->push(Post::factory()->for($category)->create());
        }

        $this->browse(function (Browser $browser) use ($posts, $categories) {
            $browser->visit('/')
                ->assertSee($posts->first()->title)
                ->press("Categories");

            foreach ($categories as $category) {
                $browser->assertSee($category->name);
            }

            $selectedCategory = $categories->first();
            $browser->clickLink($selectedCategory->name)
                ->pause(500);

            foreach ($posts as $post) {
                if ($post->category_id === $selectedCategory->id) {
                    $browser->assertSee($post->title);
                } else {
                    $browser->assertDontSee($post->title);
                }
            }
        });
    }


    public function testSearchField()
    {
        $posts = Post::factory()->count(3)->create();

        $this->browse(function (Browser $browser) use ($posts) {
            $searchQuery = substr($posts->first()->title, 0, 5);

            $browser->visit('/')
                ->waitFor('input[name="search"]', 5)
                ->type('input[name="search"]', $searchQuery)
                ->keys('input[name="search"]', '{enter}')
                ->pause(1500);

            foreach ($posts as $post) {
                if (stripos($post->title, $searchQuery) !== false) {
                    $browser->assertSee($post->title);
                } else {
                    $browser->assertDontSee($post->title);
                }
            }
        });
    }



    public function testFailSearchField()
    {
        $posts = Post::factory()->count(3)->create();

        $this->browse(function (Browser $browser) use ($posts) {
            $browser->visit('/')
                ->waitFor('input[name="search"]', 5)
                ->type('input[name="search"]', "random")
                ->keys('input[name="search"]', '{enter}')
                ->pause(1500)
                ->assertSee("No posts yet.");
        });
    }

}
