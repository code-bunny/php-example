<?php namespace Tests\Pages\Admin;

class ContactsTest extends \AdminTestCase
{
    private array $created = [];

    protected function tearDown(): void
    {
        foreach ($this->created as $id) {
            $this->apiDelete("/api/v1/contacts/$id");
        }
        parent::tearDown();
    }

    public function test_index_returns_ok(): void
    {
        $this->get('/admin/contacts')->assertOk();
    }

    public function test_edit_form_returns_ok(): void
    {
        $id = $this->apiCreateContact();
        $this->created[] = $id;

        $this->get("/admin/contacts/$id/edit")->assertOk();
    }

    public function test_update_contact_redirects(): void
    {
        $id = $this->apiCreateContact();
        $this->created[] = $id;

        $csrf = $this->fetchCsrfToken("/admin/contacts/$id/edit");

        $this->post("/admin/contacts/$id/edit", [
            'csrf_token' => $csrf,
            'email'      => 'updated@example.com',
            'message'    => 'Updated message',
        ])->assertRedirect();
    }

    public function test_update_with_invalid_email_returns_422(): void
    {
        $id = $this->apiCreateContact();
        $this->created[] = $id;

        $csrf = $this->fetchCsrfToken("/admin/contacts/$id/edit");

        $this->post("/admin/contacts/$id/edit", [
            'csrf_token' => $csrf,
            'email'      => 'not-an-email',
            'message'    => 'Some message',
        ])->assertStatus(422);
    }

    public function test_delete_contact_redirects(): void
    {
        $id = $this->apiCreateContact();

        $csrf = $this->fetchCsrfToken('/admin/contacts');

        $this->post("/admin/contacts/$id/delete", [
            'csrf_token' => $csrf,
        ])->assertRedirect();
    }
}
