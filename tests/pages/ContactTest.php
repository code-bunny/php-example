<?php namespace Tests\Pages;

class ContactTest extends \PageTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->clearRateLimit('contact');
    }

    public function test_contact_page_returns_ok(): void
    {
        $this->get('/contact')->assertOk();
    }

    public function test_contact_page_shows_form(): void
    {
        $this->get('/contact')->assertOk()->assertSee('<form');
    }

    public function test_post_with_valid_data_returns_ok(): void
    {
        $this->post('/contact', [
            'csrf_token' => $this->fetchCsrfToken(),
            'email'      => 'test@example.com',
            'message'    => 'Hello from the test suite',
        ])->assertOk();
    }

    public function test_post_with_invalid_email_returns_422(): void
    {
        $this->post('/contact', [
            'csrf_token' => $this->fetchCsrfToken(),
            'email'      => 'not-an-email',
            'message'    => 'Hello',
        ])->assertStatus(422);
    }

    public function test_post_without_message_returns_422(): void
    {
        $this->post('/contact', [
            'csrf_token' => $this->fetchCsrfToken(),
            'email'      => 'test@example.com',
            'message'    => '',
        ])->assertStatus(422);
    }

    private function fetchCsrfToken(): string
    {
        $body = $this->get('/contact')->body;
        preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $matches);
        return $matches[1] ?? '';
    }
}
