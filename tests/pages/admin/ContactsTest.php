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

    public function test_show_contact_returns_ok(): void
    {
        $id = $this->apiCreateContact();
        $this->created[] = $id;

        $this->get("/admin/contacts/$id")->assertOk();
    }

    public function test_show_displays_email_and_message(): void
    {
        $id = $this->apiCreateContact('show@example.com', 'Hello there');
        $this->created[] = $id;

        $this->get("/admin/contacts/$id")
            ->assertOk()
            ->assertSee('show@example.com')
            ->assertSee('Hello there');
    }

    public function test_delete_contact_redirects(): void
    {
        $id   = $this->apiCreateContact();
        $csrf = $this->fetchCsrfToken('/admin/contacts');

        $this->post("/admin/contacts/$id/delete", [
            'csrf_token' => $csrf,
        ])->assertRedirect();
    }
}
