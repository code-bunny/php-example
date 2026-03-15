<?php namespace Tests\Pages\Admin;

class UsersTest extends \AdminTestCase
{
    private array $created = [];

    protected function tearDown(): void
    {
        foreach ($this->created as $id) {
            $csrf = $this->fetchCsrfToken('/admin/users');
            $this->post("/admin/users/$id/delete", ['csrf_token' => $csrf]);
        }
        parent::tearDown();
    }

    public function test_index_returns_ok(): void
    {
        $this->get('/admin/users')->assertOk();
    }

    public function test_new_form_returns_ok(): void
    {
        $this->get('/admin/users/new')->assertOk();
    }

    public function test_create_user_redirects(): void
    {
        $csrf  = $this->fetchCsrfToken('/admin/users/new');
        $email = 'newuser_' . uniqid() . '@example.com';

        $response = $this->post('/admin/users/new', [
            'csrf_token' => $csrf,
            'email'      => $email,
            'password'   => 'password123',
            'role'       => 'admin',
        ])->assertRedirect();

        // List is newest-first; the user we just created is the first match
        $body = $this->get('/admin/users')->body;
        preg_match_all('#/admin/users/([0-9a-f-]{36})/edit#', $body, $m);
        if (!empty($m[1])) {
            $this->created[] = $m[1][0];
        }
    }

    public function test_create_with_duplicate_email_returns_422(): void
    {
        $csrf = $this->fetchCsrfToken('/admin/users/new');

        $this->post('/admin/users/new', [
            'csrf_token' => $csrf,
            'email'      => TEST_ADMIN_EMAIL,
            'password'   => 'password123',
            'role'       => 'admin',
        ])->assertStatus(422)->assertSee('already taken');
    }

    public function test_create_with_invalid_email_returns_422(): void
    {
        $csrf = $this->fetchCsrfToken('/admin/users/new');

        $this->post('/admin/users/new', [
            'csrf_token' => $csrf,
            'email'      => 'not-an-email',
            'password'   => 'password123',
            'role'       => 'admin',
        ])->assertStatus(422);
    }

    public function test_create_without_password_returns_422(): void
    {
        $csrf = $this->fetchCsrfToken('/admin/users/new');

        $this->post('/admin/users/new', [
            'csrf_token' => $csrf,
            'email'      => 'nopass_' . uniqid() . '@example.com',
            'password'   => '',
            'role'       => 'admin',
        ])->assertStatus(422);
    }

    public function test_edit_form_returns_ok(): void
    {
        $id = $this->createUser();

        $this->get("/admin/users/$id/edit")->assertOk();
    }

    public function test_update_user_redirects(): void
    {
        $id   = $this->createUser();
        $csrf = $this->fetchCsrfToken("/admin/users/$id/edit");

        $this->post("/admin/users/$id/edit", [
            'csrf_token' => $csrf,
            'email'      => 'updated_' . uniqid() . '@example.com',
            'password'   => '',
            'role'       => 'admin',
        ])->assertRedirect();
    }

    public function test_update_with_short_password_returns_422(): void
    {
        $id   = $this->createUser();
        $csrf = $this->fetchCsrfToken("/admin/users/$id/edit");

        $this->post("/admin/users/$id/edit", [
            'csrf_token' => $csrf,
            'email'      => 'valid_' . uniqid() . '@example.com',
            'password'   => 'short',
            'role'       => 'admin',
        ])->assertStatus(422);
    }

    public function test_delete_user_redirects(): void
    {
        $id   = $this->createUser();
        $csrf = $this->fetchCsrfToken('/admin/users');

        $this->post("/admin/users/$id/delete", [
            'csrf_token' => $csrf,
        ])->assertRedirect();

        // Remove from created so tearDown doesn't try to delete again
        $this->created = array_filter($this->created, fn($i) => $i !== $id);
    }

    private function createUser(): string
    {
        $csrf  = $this->fetchCsrfToken('/admin/users/new');
        $email = 'testuser_' . uniqid() . '@example.com';

        $this->post('/admin/users/new', [
            'csrf_token' => $csrf,
            'email'      => $email,
            'password'   => 'password123',
            'role'       => 'admin',
        ]);

        // List is newest-first; the user we just created is the first match
        $body = $this->get('/admin/users')->body;
        preg_match_all('#/admin/users/([0-9a-f-]{36})/edit#', $body, $m);
        $id = $m[1][0];
        $this->created[] = $id;
        return $id;
    }
}
