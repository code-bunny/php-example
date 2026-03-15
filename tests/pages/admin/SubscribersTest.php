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

    public function test_unsubscribe_redirects(): void
    {
        $id   = $this->apiCreateSubscriber('unsub_' . uniqid() . '@example.com');
        $csrf = $this->fetchCsrfToken('/admin/subscribers');

        $this->post("/admin/subscribers/$id/delete", [
            'csrf_token' => $csrf,
        ])->assertRedirect();
    }
}
