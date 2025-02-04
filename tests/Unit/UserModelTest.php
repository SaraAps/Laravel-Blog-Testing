<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_posts()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Post::class, $user->posts->first());
        $this->assertEquals($post->id, $user->posts->first()->id);
    }


    /** @test */
    public function it_casts_email_verified_at_to_datetime()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->assertInstanceOf(\DateTimeInterface::class, $user->email_verified_at);
    }

    /** @test */
    public function it_hashes_password_using_mutator()
    {
        $user = User::factory()->create(['password' => 'Password123@']);

        $this->assertNotEquals('Password123@', $user->password);
        $this->assertTrue(password_verify('Password123@', $user->password));
    }
}
