<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_all_posts()
    {
        Post::factory()->count(5)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('posts.index');
        $response->assertViewHas('posts');
    }

    /** @test */
    public function it_shows_a_single_post()
    {
        $post = Post::factory()->create();

        $response = $this->get('/posts/' . $post->slug);

        $response->assertStatus(200);
        $response->assertViewIs('posts.show');
        $response->assertViewHas('post', $post);
    }
}
