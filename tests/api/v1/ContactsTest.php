<?php namespace Tests\Api\V1;

class ContactsTest extends \TestCase
{
    private array $created = [];

    protected function tearDown(): void
    {
        foreach ($this->created as $id) {
            $this->delete("/api/v1/contacts/$id");
        }
    }

    public function test_get_contacts_returns_ok(): void
    {
        $this->get('/api/v1/contacts')->assertOk();
    }

    public function test_create_contact(): void
    {
        $response = $this->post('/api/v1/contacts', [
            'data' => ['type' => 'contacts', 'attributes' => ['email' => 'test@example.com', 'message' => 'Hello']],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.attributes.email', 'test@example.com');

        $this->created[] = $response->json['data']['id'];
    }

    public function test_create_with_invalid_email_returns_422(): void
    {
        $this->post('/api/v1/contacts', [
            'data' => ['type' => 'contacts', 'attributes' => ['email' => 'not-an-email', 'message' => 'Hi']],
        ])->assertStatus(422);
    }

    public function test_create_without_message_returns_422(): void
    {
        $this->post('/api/v1/contacts', [
            'data' => ['type' => 'contacts', 'attributes' => ['email' => 'test@example.com']],
        ])->assertStatus(422);
    }

    public function test_show_contact(): void
    {
        $id = $this->createContact();

        $this->get("/api/v1/contacts/$id")
            ->assertOk()
            ->assertJsonPath('data.id', $id);
    }

    public function test_show_unknown_id_returns_404(): void
    {
        $this->get('/api/v1/contacts/00000000-0000-0000-0000-000000000000')
            ->assertStatus(404);
    }

    public function test_update_contact(): void
    {
        $id = $this->createContact();

        $this->patch("/api/v1/contacts/$id", [
            'data' => ['type' => 'contacts', 'attributes' => ['message' => 'Updated message']],
        ])->assertOk()
          ->assertJsonPath('data.attributes.message', 'Updated message');
    }

    public function test_delete_contact(): void
    {
        $id = $this->createContact();

        $this->delete("/api/v1/contacts/$id")->assertNoContent();
        $this->get("/api/v1/contacts/$id")->assertStatus(404);
    }

    private function createContact(): string
    {
        $response = $this->post('/api/v1/contacts', [
            'data' => ['type' => 'contacts', 'attributes' => ['email' => 'test@example.com', 'message' => 'Test message']],
        ]);
        $id = $response->json['data']['id'];
        $this->created[] = $id;
        return $id;
    }
}
