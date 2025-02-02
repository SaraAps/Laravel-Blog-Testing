<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostCommentsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_stores_a_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user);
        $response = $this->post('/posts/' . $post->slug . '/comments', [
            'body' => 'This is a comment.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'body' => 'This is a comment.',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_fails_to_store_a_comment_without_a_body()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user);
        $response = $this->post('/posts/' . $post->slug . '/comments', [
            'body' => '',
        ]);

        $response->assertSessionHasErrors('body');
    }
}
