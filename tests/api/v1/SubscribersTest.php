<?php namespace Tests\Api\V1;

class SubscribersTest extends \TestCase
{
    private array $created = [];

    protected function tearDown(): void
    {
        foreach ($this->created as $id) {
            $this->delete("/api/v1/subscribers/$id");
        }
    }

    public function test_get_subscribers_returns_ok(): void
    {
        $this->get('/api/v1/subscribers')->assertOk();
    }

    public function test_create_subscriber(): void
    {
        $response = $this->post('/api/v1/subscribers', [
            'data' => ['type' => 'subscribers', 'attributes' => ['email' => 'sub@example.com']],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.attributes.email', 'sub@example.com');

        $this->created[] = $response->json['data']['id'];
    }

    public function test_duplicate_email_returns_409(): void
    {
        $id = $this->createSubscriber('dup@example.com');

        $this->post('/api/v1/subscribers', [
            'data' => ['type' => 'subscribers', 'attributes' => ['email' => 'dup@example.com']],
        ])->assertStatus(409);
    }

    public function test_create_with_invalid_email_returns_422(): void
    {
        $this->post('/api/v1/subscribers', [
            'data' => ['type' => 'subscribers', 'attributes' => ['email' => 'not-valid']],
        ])->assertStatus(422);
    }

    public function test_show_subscriber(): void
    {
        $id = $this->createSubscriber('show@example.com');

        $this->get("/api/v1/subscribers/$id")
            ->assertOk()
            ->assertJsonPath('data.id', $id);
    }

    public function test_show_unknown_id_returns_404(): void
    {
        $this->get('/api/v1/subscribers/00000000-0000-0000-0000-000000000000')
            ->assertStatus(404);
    }

    public function test_delete_subscriber(): void
    {
        $id = $this->createSubscriber('del@example.com');

        $this->delete("/api/v1/subscribers/$id")->assertNoContent();
        $this->get("/api/v1/subscribers/$id")->assertStatus(404);
    }

    private function createSubscriber(string $email = 'sub@example.com'): string
    {
        $response = $this->post('/api/v1/subscribers', [
            'data' => ['type' => 'subscribers', 'attributes' => ['email' => $email]],
        ]);
        $id = $response->json['data']['id'];
        $this->created[] = $id;
        return $id;
    }
}
