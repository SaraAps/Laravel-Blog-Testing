<?php

namespace Tests\Feature;


use App\Services\MailchimpNewsletter;
use MailchimpMarketing\ApiClient;
use Mockery;
use Tests\TestCase;

class MailchimpNewsletterTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_subscribes_a_user_to_a_mailchimp_list()
    {
        $mockClient = Mockery::mock(ApiClient::class);
        $mockClient->shouldReceive('lists->addListMember')
            ->once()
            ->with('subscribers-list-id', [
                'email_address' => 'test@example.com',
                'status' => 'subscribed',
            ])
            ->andReturn(['id' => '12345']); // Simulate a successful response

        $newsletter = new MailchimpNewsletter($mockClient);
        $response = $newsletter->subscribe('test@example.com', 'subscribers-list-id');

        $this->assertEquals(['id' => '12345'], $response);
    }

    /** @test */
    public function it_subscribes_a_user_to_the_default_list_if_no_list_is_provided()
    {
        $mockClient = Mockery::mock(ApiClient::class);
        $mockClient->shouldReceive('lists->addListMember')
            ->once()
            ->with('default-list-id', [
                'email_address' => 'test@example.com',
                'status' => 'subscribed',
            ])
            ->andReturn(['id' => '12345']); // Simulate a successful response

        config(['services.mailchimp.lists.subscribers' => 'default-list-id']);

        $newsletter = new MailchimpNewsletter($mockClient);
        $response = $newsletter->subscribe('test@example.com');

        $this->assertEquals(['id' => '12345'], $response);
    }

    /** @test */
    public function it_throws_an_exception_if_the_api_call_fails()
    {
        $mockClient = Mockery::mock(ApiClient::class);
        $mockClient->shouldReceive('lists->addListMember')
            ->once()
            ->with('subscribers-list-id', [
                'email_address' => 'test@example.com',
                'status' => 'subscribed',
            ])
            ->andThrow(new \Exception('API call failed'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API call failed');

        $newsletter = new MailchimpNewsletter($mockClient);
        $newsletter->subscribe('test@example.com', 'subscribers-list-id');
    }
}
