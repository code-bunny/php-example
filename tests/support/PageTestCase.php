<?php

use PHPUnit\Framework\TestCase as BaseTestCase;

// Base class for testing public HTML pages — no API key, no JSON content-type.
// A cookie jar file is shared across all requests in one test so that the
// session (and therefore the CSRF token) persists between GET and POST.
abstract class PageTestCase extends BaseTestCase
{
    private string $cookieJar;

    protected function setUp(): void
    {
        $this->cookieJar = tempnam(sys_get_temp_dir(), 'phptest_cookies_');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->cookieJar)) {
            unlink($this->cookieJar);
        }
    }

    protected function get(string $path): PageResponse
    {
        return $this->request('GET', $path);
    }

    protected function post(string $path, array $fields = []): PageResponse
    {
        return $this->request('POST', $path, $fields);
    }

    // Rate limit files are stored in sys_get_temp_dir() by the server.
    // Call this in setUp() for any test that POSTs to a rate-limited route.
    protected function clearRateLimit(string $key): void
    {
        foreach (['::1', '127.0.0.1'] as $ip) {
            $file = sys_get_temp_dir() . '/rl_' . md5($ip . $key) . '.json';
            if (file_exists($file)) unlink($file);
        }
    }

    // Subclasses can override this to inject extra curl options (e.g. Basic Auth)
    protected function extraCurlOptions(): array
    {
        return [];
    }

    // ── Internals ────────────────────────────────────────────────────

    private function request(string $method, string $path, array $fields = []): PageResponse
    {
        $url = (getenv('APP_URL') ?: 'http://localhost:8000') . $path;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false, // test redirects explicitly
            CURLOPT_COOKIEFILE     => $this->cookieJar, // read cookies
            CURLOPT_COOKIEJAR      => $this->cookieJar, // write cookies
        ] + $this->extraCurlOptions());

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        }

        $body   = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return new PageResponse($status, $body ?: '');
    }
}
