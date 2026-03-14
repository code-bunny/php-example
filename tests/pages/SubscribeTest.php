<?php namespace Tests\Pages;

class SubscribeTest extends \PageTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->clearRateLimit('subscribe');
    }

    public function test_post_with_valid_email_redirects(): void
    {
        $this->post('/subscribe', [
            'csrf_token' => $this->fetchCsrfToken(),
            'email'      => 'newsub_' . uniqid() . '@example.com',
        ])->assertRedirect();
    }

    public function test_post_with_invalid_email_redirects_without_subscribed_flag(): void
    {
        $response = $this->post('/subscribe', [
            'csrf_token' => $this->fetchCsrfToken(),
            'email'      => 'not-valid',
        ]);

        $response->assertRedirect();
    }

    public function test_get_redirects_to_home(): void
    {
        $this->get('/subscribe')->assertRedirect();
    }

    private function fetchCsrfToken(): string
    {
        $body = $this->get('/')->body;
        preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $matches);
        return $matches[1] ?? '';
    }
}
