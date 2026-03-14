<?php

// Base class for admin page tests — adds HTTP Basic Auth to every request.
// Uses the same ADMIN_USER / ADMIN_PASS credentials as the app reads from .env.
abstract class AdminTestCase extends PageTestCase
{
    protected function extraCurlOptions(): array
    {
        $user = $_ENV['ADMIN_USER'] ?? 'admin';
        $pass = $_ENV['ADMIN_PASS'] ?? 'password';
        return [CURLOPT_USERPWD => "$user:$pass"];
    }

    // Fetch a CSRF token by loading any admin page that has a form.
    // The cookie jar keeps the same session, so the token stays valid for the POST.
    protected function fetchCsrfToken(string $path = '/admin'): string
    {
        $body = $this->get($path)->body;
        preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $matches);
        return $matches[1] ?? '';
    }

    // Create a post via the API and return its id — used for test setup/teardown.
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
        $url = ($_ENV['APP_URL'] ?? 'http://localhost:8000') . $path;
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . TEST_API_KEY],
        ]);
        curl_exec($ch);
    }

    // ── Internals ────────────────────────────────────────────────────

    private function apiPost(string $path, array $body): string
    {
        $url = ($_ENV['APP_URL'] ?? 'http://localhost:8000') . $path;
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
