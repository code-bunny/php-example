<?php

// Base class for admin page tests — logs in via POST /admin/login before each test.
abstract class AdminTestCase extends PageTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAsAdmin();
    }

    private function loginAsAdmin(): void
    {
        // Fetch the login page first to get a CSRF token (and set the session cookie)
        $body = $this->get('/admin/login')->body;
        preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $matches);
        $csrf = $matches[1] ?? '';

        $this->post('/admin/login', [
            'email'      => TEST_ADMIN_EMAIL,
            'password'   => TEST_ADMIN_PASSWORD,
            'csrf_token' => $csrf,
        ]);
        // The cookie jar now holds the authenticated session for all subsequent requests
    }

    // Fetch a CSRF token by loading any admin page that has a form.
    protected function fetchCsrfToken(string $path = '/admin'): string
    {
        $body = $this->get($path)->body;
        preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $matches);
        return $matches[1] ?? '';
    }

    protected function apiCreatePost(string $title = 'Test Post', string $body = 'Test body'): string
    {
        return $this->apiPost('/api/v1/posts', [
            'data' => ['type' => 'posts', 'attributes' => ['title' => $title, 'body' => $body]],
        ]);
    }

    protected function apiCreateSubscriber(string $email): string
    {
        return $this->apiPost('/api/v1/subscribers', [
            'data' => ['type' => 'subscribers', 'attributes' => ['email' => $email]],
        ]);
    }

    protected function apiCreateContact(string $email = 'test@example.com', string $message = 'Test'): string
    {
        return $this->apiPost('/api/v1/contacts', [
            'data' => ['type' => 'contacts', 'attributes' => ['email' => $email, 'message' => $message]],
        ]);
    }

    protected function apiDelete(string $path): void
    {
        $url = (getenv('APP_URL') ?: 'http://localhost:8001') . $path;
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . TEST_API_KEY],
        ]);
        curl_exec($ch);
    }

    private function apiPost(string $path, array $body): string
    {
        $url = (getenv('APP_URL') ?: 'http://localhost:8001') . $path;
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/vnd.api+json',
                'Authorization: Bearer ' . TEST_API_KEY,
            ],
        ]);
        $response = json_decode(curl_exec($ch), true);
        return $response['data']['id'];
    }
}
