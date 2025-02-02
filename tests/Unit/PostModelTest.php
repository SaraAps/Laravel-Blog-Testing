<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_category_relationship()
    {
        $category = Category::factory()->create();
        $post = Post::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $post->category);
        $this->assertEquals($category->id, $post->category->id);
    }

    /** @test */
    public function it_has_an_author_relationship()
    {
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);

        $this->assertInstanceOf(User::class, $post->author);
        $this->assertEquals($author->id, $post->author->id);
    }

    /** @test */
    public function it_has_many_comments()
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $this->assertInstanceOf(Comment::class, $post->comments->first());
        $this->assertEquals($comment->id, $post->comments->first()->id);
    }

    /** @test */
    public function it_filters_posts_by_search_term()
    {
        $post1 = Post::factory()->create(['title' => 'Laravel is awesome']);
        $post2 = Post::factory()->create(['title' => 'PHP is great']);

        $filteredPosts = Post::filter(['search' => 'Laravel'])->get();

        $this->assertTrue($filteredPosts->contains($post1));
        $this->assertFalse($filteredPosts->contains($post2));
    }

    /** @test */
    public function it_filters_posts_by_category_slug()
    {
        $category = Category::factory()->create(['slug' => 'laravel']);
        $post1 = Post::factory()->create(['category_id' => $category->id]);
        $post2 = Post::factory()->create();

        $filteredPosts = Post::filter(['category' => 'laravel'])->get();

        $this->assertTrue($filteredPosts->contains($post1));
        $this->assertFalse($filteredPosts->contains($post2));
    }

    /** @test */
    public function it_filters_posts_by_author_username()
    {
        $author = User::factory()->create(['username' => 'john_doe']);
        $post1 = Post::factory()->create(['user_id' => $author->id]);
        $post2 = Post::factory()->create();

        $filteredPosts = Post::filter(['author' => 'john_doe'])->get();

        $this->assertTrue($filteredPosts->contains($post1));
        $this->assertFalse($filteredPosts->contains($post2));
    }

    /** @test */
    public function it_eager_loads_category_and_author_by_default()
    {
        $post = Post::factory()->create();

        $fetchedPost = Post::first();

        $this->assertTrue($fetchedPost->relationLoaded('category'));
        $this->assertTrue($fetchedPost->relationLoaded('author'));
    }
}
