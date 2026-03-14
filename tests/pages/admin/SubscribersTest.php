<?php namespace Tests\Pages\Admin;

class SubscribersTest extends \AdminTestCase
{
    private array $created = [];

    protected function tearDown(): void
    {
        foreach ($this->created as $id) {
            $this->apiDelete("/api/v1/subscribers/$id");
        }
        parent::tearDown();
    }

    public function test_index_returns_ok(): void
    {
        $this->get('/admin/subscribers')->assertOk();
    }

    public function test_new_form_returns_ok(): void
    {
        $this->get('/admin/subscribers/new')->assertOk();
    }

    public function test_create_subscriber_redirects(): void
    {
        $csrf  = $this->fetchCsrfToken('/admin/subscribers/new');
        $email = 'adminsub_' . uniqid() . '@example.com';

        $this->post('/admin/subscribers/new', [
            'csrf_token' => $csrf,
            'email'      => $email,
        ])->assertRedirect();
    }

    public function test_create_with_invalid_email_returns_422(): void
    {
        $csrf = $this->fetchCsrfToken('/admin/subscribers/new');

        $this->post('/admin/subscribers/new', [
            'csrf_token' => $csrf,
            'email'      => 'not-valid',
        ])->assertStatus(422);
    }

    public function test_edit_form_returns_ok(): void
    {
        $id = $this->apiCreateSubscriber('edit_' . uniqid() . '@example.com');
        $this->created[] = $id;

        $this->get("/admin/subscribers/$id/edit")->assertOk();
    }

    public function test_update_subscriber_redirects(): void
    {
        $id = $this->apiCreateSubscriber('update_' . uniqid() . '@example.com');
        $this->created[] = $id;

        $csrf = $this->fetchCsrfToken("/admin/subscribers/$id/edit");

        $this->post("/admin/subscribers/$id/edit", [
            'csrf_token' => $csrf,
            'email'      => 'newaddr_' . uniqid() . '@example.com',
        ])->assertRedirect();
    }

    public function test_delete_subscriber_redirects(): void
    {
        $id = $this->apiCreateSubscriber('del_' . uniqid() . '@example.com');

        $csrf = $this->fetchCsrfToken('/admin/subscribers');

        $this->post("/admin/subscribers/$id/delete", [
            'csrf_token' => $csrf,
        ])->assertRedirect();
    }
}
