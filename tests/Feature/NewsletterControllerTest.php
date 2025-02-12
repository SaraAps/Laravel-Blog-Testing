<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\Newsletter;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class NewsletterControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_subscribes_an_email_to_the_newsletter()
    {
        $newsletterMock = Mockery::mock(Newsletter::class);
        $newsletterMock->shouldReceive('subscribe')
            ->once()
            ->with('test@example.com');

        $this->app->instance(Newsletter::class, $newsletterMock);

        $response = $this->post('/newsletter', [
            'email' => 'test@example.com',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'You are now signed up for our newsletter!');
    }

    /** @test */
    public function it_fails_to_subscribe_with_an_invalid_email()
    {
        $response = $this->post('/newsletter', [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_handles_newsletter_subscription_errors()
    {
        $newsletterMock = Mockery::mock(Newsletter::class);
        $newsletterMock->shouldReceive('subscribe')
            ->once()
            ->with('test@example.com')
            ->andThrow(new \Exception('Failed to subscribe'));

        $this->app->instance(Newsletter::class, $newsletterMock);

        $response = $this->post('/newsletter', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('email', 'This email could not be added to our newsletter list.');
    }

    /** @test */
    public function it_requires_an_email_to_subscribe()
    {
        $response = $this->post('/newsletter', [
            'email' => '',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_handles_unexpected_errors_gracefully()
    {
        $newsletterMock = Mockery::mock(Newsletter::class);
        $newsletterMock->shouldReceive('subscribe')
            ->once()
            ->with('test@example.com')
            ->andThrow(new \RuntimeException('Unexpected error'));

        $this->app->instance(Newsletter::class, $newsletterMock);

        $response = $this->post('/newsletter', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('email', 'This email could not be added to our newsletter list.');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
