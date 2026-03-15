<?php namespace Tests\Pages\Admin;

// Login tests use PageTestCase directly — no pre-authenticated session.
class LoginTest extends \PageTestCase
{
    public function test_login_page_returns_ok(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_valid_credentials_redirect_to_admin(): void
    {
        $body = $this->get('/admin/login')->body;
        preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $m);

        $this->post('/admin/login', [
            'csrf_token' => $m[1] ?? '',
            'email'      => TEST_ADMIN_EMAIL,
            'password'   => TEST_ADMIN_PASSWORD,
        ])->assertRedirect();
    }

    public function test_wrong_password_returns_422(): void
    {
        $body = $this->get('/admin/login')->body;
        preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $m);

        $this->post('/admin/login', [
            'csrf_token' => $m[1] ?? '',
            'email'      => TEST_ADMIN_EMAIL,
            'password'   => 'wrong-password',
        ])->assertStatus(422)->assertSee('Invalid email or password');
    }

    public function test_unknown_email_returns_422(): void
    {
        $body = $this->get('/admin/login')->body;
        preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $m);

        $this->post('/admin/login', [
            'csrf_token' => $m[1] ?? '',
            'email'      => 'nobody@example.com',
            'password'   => 'anything',
        ])->assertStatus(422)->assertSee('Invalid email or password');
    }

    public function test_unauthenticated_admin_redirects_to_login(): void
    {
        // Fresh request with no session — should redirect, not 200 or 401
        $this->get('/admin')->assertRedirect();
    }

    public function test_logout_redirects_to_login(): void
    {
        // Log in first
        $body = $this->get('/admin/login')->body;
        preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $m);
        $this->post('/admin/login', [
            'csrf_token' => $m[1] ?? '',
            'email'      => TEST_ADMIN_EMAIL,
            'password'   => TEST_ADMIN_PASSWORD,
        ]);

        // Now fetch a page to get a fresh CSRF token, then log out
        $body = $this->get('/admin')->body;
        preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $m);

        $this->post('/admin/logout', [
            'csrf_token' => $m[1] ?? '',
        ])->assertRedirect();
    }
}
