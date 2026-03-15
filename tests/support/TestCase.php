<?php

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // ── HTTP helpers ─────────────────────────────────────────────────

    protected function get(string $path): TestResponse
    {
        return $this->request('GET', $path);
    }

    protected function post(string $path, array $body = []): TestResponse
    {
        return $this->request('POST', $path, $body);
    }

    protected function patch(string $path, array $body = []): TestResponse
    {
        return $this->request('PATCH', $path, $body);
    }

    protected function delete(string $path): TestResponse
    {
        return $this->request('DELETE', $path);
    }

    // ── Internals ────────────────────────────────────────────────────

    private function request(string $method, string $path, ?array $body = null): TestResponse
    {
        $url = (getenv('APP_URL') ?: 'http://localhost:8000') . $path;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/vnd.api+json',
                'Accept: application/vnd.api+json',
                'Authorization: Bearer ' . TEST_API_KEY,
            ],
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $raw    = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return new TestResponse($status, $raw ?: '');
    }
}
