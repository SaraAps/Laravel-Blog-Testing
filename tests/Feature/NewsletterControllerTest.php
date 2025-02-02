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
        // Mock the Newsletter service
        $newsletterMock = Mockery::mock(Newsletter::class);
        $newsletterMock->shouldReceive('subscribe')
            ->once()
            ->with('test@example.com');

        // Bind the mock instance in the service container
        $this->app->instance(Newsletter::class, $newsletterMock);

        // Make a request to the newsletter endpoint
        $response = $this->post('/newsletter', [
            'email' => 'test@example.com',
        ]);

        // Assert the response
        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'You are now signed up for our newsletter!');
    }

    /** @test */
    public function it_fails_to_subscribe_with_an_invalid_email()
    {
        // Make a request with an invalid email
        $response = $this->post('/newsletter', [
            'email' => 'invalid-email',
        ]);

        // Assert validation errors
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_handles_newsletter_subscription_errors()
    {
        // Mock the Newsletter service to throw an exception
        $newsletterMock = Mockery::mock(Newsletter::class);
        $newsletterMock->shouldReceive('subscribe')
            ->once()
            ->with('test@example.com')
            ->andThrow(new \Exception('Failed to subscribe'));

        // Bind the mock instance in the service container
        $this->app->instance(Newsletter::class, $newsletterMock);

        // Make a request to the newsletter endpoint
        $response = $this->post('/newsletter', [
            'email' => 'test@example.com',
        ]);

        // Assert validation errors
        $response->assertSessionHasErrors('email', 'This email could not be added to our newsletter list.');
    }

    /** @test */
    public function it_requires_an_email_to_subscribe()
    {
        // Make a request without an email
        $response = $this->post('/newsletter', [
            'email' => '',
        ]);

        // Assert validation errors
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_handles_unexpected_errors_gracefully()
    {
        // Mock the Newsletter service to throw an unexpected exception
        $newsletterMock = Mockery::mock(Newsletter::class);
        $newsletterMock->shouldReceive('subscribe')
            ->once()
            ->with('test@example.com')
            ->andThrow(new \RuntimeException('Unexpected error'));

        // Bind the mock instance in the service container
        $this->app->instance(Newsletter::class, $newsletterMock);

        // Make a request to the newsletter endpoint
        $response = $this->post('/newsletter', [
            'email' => 'test@example.com',
        ]);

        // Assert validation errors
        $response->assertSessionHasErrors('email', 'This email could not be added to our newsletter list.');
    }

    protected function tearDown(): void
    {
        // Clean up Mockery after each test
        Mockery::close();
        parent::tearDown();
    }
}
