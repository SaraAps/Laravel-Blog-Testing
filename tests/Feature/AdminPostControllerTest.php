<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminPostControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_all_posts_in_admin_panel()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Post::factory()->count(5)->create();

        $this->actingAs($admin);
        $response = $this->get('/admin/posts');

        $response->assertStatus(200);
        $response->assertViewIs('admin.posts.index');
        $response->assertViewHas('posts');
    }

    /** @test */
    public function it_shows_the_create_post_form()
    {
        $this->withoutMiddleware([VerifyCsrfToken::class]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin);
        $response = $this->get('/admin/posts/create');

        $response->assertStatus(200);
        $response->assertViewIs('admin.posts.create');
    }

    /** @test */
    public function it_stores_a_new_post()
    {
        $this->withoutMiddleware([VerifyCsrfToken::class]);
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create();

        $this->actingAs($admin);
        $response = $this->post('/admin/posts', [
            'title' => 'New Post',
            'thumbnail' => UploadedFile::fake()->image('thumbnail.jpg'),
            'slug' => 'new-post',
            'excerpt' => 'This is an excerpt.',
            'body' => 'This is the body.',
            'category_id' => $category->id,
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('posts', [
            'title' => 'New Post',
            'slug' => 'new-post',
        ]);
    }

    /** @test */
    public function it_shows_the_edit_post_form()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::factory()->create();

        $this->actingAs($admin);
        $response = $this->get('/admin/posts/' . $post->id . '/edit');

        $response->assertStatus(200);
        $response->assertViewIs('admin.posts.edit');
        $response->assertViewHas('post', $post);
    }

    /** @test */
    public function it_updates_an_existing_post()
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin);
        $response = $this->patch('/admin/posts/' . $post->id, [
            'title' => 'Updated Post',
            'thumbnail' => UploadedFile::fake()->image('updated-thumbnail.jpg'),
            'slug' => 'updated-post',
            'excerpt' => 'Updated excerpt.',
            'body' => 'Updated body.',
            'category_id' => $category->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'title' => 'Updated Post',
            'slug' => 'updated-post',
        ]);
    }

    /** @test */
    public function it_deletes_a_post()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::factory()->create();

        $this->actingAs($admin);
        $response = $this->delete('/admin/posts/' . $post->id);

        $response->assertRedirect();
        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    /** @test */
    public function it_validates_post_creation()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin);
        $response = $this->post('/admin/posts', [
            'title' => '',
            'thumbnail' => '',
            'slug' => '',
            'excerpt' => '',
            'body' => '',
            'category_id' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'thumbnail', 'slug', 'excerpt', 'body', 'category_id']);
    }

    /** @test */
    public function it_validates_post_update()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $post = Post::factory()->create();

        $this->actingAs($admin);
        $response = $this->patch('/admin/posts/' . $post->id, [
            'title' => '',
            'thumbnail' => '',
            'slug' => '',
            'excerpt' => '',
            'body' => '',
            'category_id' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'slug', 'excerpt', 'body', 'category_id']);
    }
}
