<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;

class PostModelTest extends TestCase
{
    public function test_post_belongs_to_user()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $post->author);
    }

    public function test_fillable_attributes()
    {
        $post = new Post();
        $fillable = ['title', 'content', 'image', 'user_id'];
        $this->assertEquals($fillable, $post->getFillable());
    }
}
