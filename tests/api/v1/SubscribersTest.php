<?php namespace Tests\Api\V1;

class SubscribersTest extends \TestCase
{
    private array $created = [];
    private string $prefix;

    protected function setUp(): void
    {
        $this->prefix = bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        foreach ($this->created as $id) {
            $this->delete("/api/v1/subscribers/$id");
        }
    }

    private function email(string $name): string
    {
        return "$name+{$this->prefix}@example.com";
    }

    public function test_get_subscribers_returns_ok(): void
    {
        $this->get('/api/v1/subscribers')->assertOk();
    }

    public function test_create_subscriber(): void
    {
        $email = $this->email('sub');
        $response = $this->post('/api/v1/subscribers', [
            'data' => ['type' => 'subscribers', 'attributes' => ['email' => $email]],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.attributes.email', $email);

        $this->created[] = $response->json['data']['id'];
    }

    public function test_duplicate_email_returns_409(): void
    {
        $id = $this->createSubscriber($this->email('dup'));

        $this->post('/api/v1/subscribers', [
            'data' => ['type' => 'subscribers', 'attributes' => ['email' => $this->email('dup')]],
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
        $id = $this->createSubscriber($this->email('show'));

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
        $id = $this->createSubscriber($this->email('del'));

        $this->delete("/api/v1/subscribers/$id")->assertNoContent();
        $this->get("/api/v1/subscribers/$id")->assertStatus(404);
    }

    private function createSubscriber(string $email): string
    {
        $response = $this->post('/api/v1/subscribers', [
            'data' => ['type' => 'subscribers', 'attributes' => ['email' => $email]],
        ]);
        $id = $response->json['data']['id'];
        $this->created[] = $id;
        return $id;
    }
}
