<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_post()
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $this->assertInstanceOf(Post::class, $comment->post);
        $this->assertEquals($post->id, $comment->post->id);
    }

    /** @test */
    public function it_belongs_to_an_author()
    {
        $author = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $author->id]);

        $this->assertInstanceOf(User::class, $comment->author);
        $this->assertEquals($author->id, $comment->author->id);
    }
}
