<?php namespace Tests\Pages\Admin;

class DashboardTest extends \AdminTestCase
{
    public function test_dashboard_returns_ok(): void
    {
        $this->get('/admin')->assertOk();
    }

    public function test_dashboard_requires_auth(): void
    {
        $url = ($_ENV['APP_URL'] ?? 'http://localhost:8000') . '/admin';
        $ch  = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true]);
        curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $this->assertSame(401, $status);
    }

    public function test_dashboard_shows_admin_heading(): void
    {
        $this->get('/admin')->assertOk()->assertSee('Admin');
    }
}
