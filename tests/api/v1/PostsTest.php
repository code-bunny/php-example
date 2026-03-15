<?php namespace Tests\Api\V1;

class PostsTest extends \TestCase
{
    private array $created = [];

    protected function tearDown(): void
    {
        foreach ($this->created as $id) {
            $this->delete("/api/v1/posts/$id");
        }
    }

    // ── Index ────────────────────────────────────────────────────────

    public function test_get_posts_returns_ok(): void
    {
        $this->get('/api/v1/posts')->assertOk();
    }

    public function test_get_posts_returns_json_api_format(): void
    {
        $this->get('/api/v1/posts')
            ->assertOk()
            ->assertJsonHas('data')
            ->assertJsonHas('links');
    }

    // ── Create ───────────────────────────────────────────────────────

    public function test_create_post(): void
    {
        $title    = 'Hello ' . uniqid();
        $response = $this->post('/api/v1/posts', [
            'data' => ['type' => 'posts', 'attributes' => ['title' => $title, 'body' => 'World']],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.type', 'posts')
            ->assertJsonPath('data.attributes.title', $title);

        $this->created[] = $response->json['data']['id'];
    }

    public function test_create_without_title_returns_422(): void
    {
        $this->post('/api/v1/posts', [
            'data' => ['type' => 'posts', 'attributes' => ['body' => 'No title']],
        ])->assertStatus(422);
    }

    public function test_create_without_body_returns_422(): void
    {
        $this->post('/api/v1/posts', [
            'data' => ['type' => 'posts', 'attributes' => ['title' => 'No body']],
        ])->assertStatus(422);
    }

    // ── Show ─────────────────────────────────────────────────────────

    public function test_show_post(): void
    {
        $title = 'Show Test ' . uniqid();
        $id    = $this->createPost($title);

        $this->get("/api/v1/posts/$id")
            ->assertOk()
            ->assertJsonPath('data.id', $id)
            ->assertJsonPath('data.attributes.title', $title);
    }

    public function test_show_unknown_id_returns_404(): void
    {
        $this->get('/api/v1/posts/00000000-0000-0000-0000-000000000000')
            ->assertStatus(404);
    }

    // ── Update ───────────────────────────────────────────────────────

    public function test_update_post(): void
    {
        $id    = $this->createPost();
        $after = 'After ' . uniqid();

        $this->patch("/api/v1/posts/$id", [
            'data' => ['type' => 'posts', 'attributes' => ['title' => $after]],
        ])->assertOk()
          ->assertJsonPath('data.attributes.title', $after);
    }

    // ── Destroy ──────────────────────────────────────────────────────

    public function test_delete_post(): void
    {
        $id = $this->createPost();

        $this->delete("/api/v1/posts/$id")->assertNoContent();
        $this->get("/api/v1/posts/$id")->assertStatus(404);
    }

    // ── Auth ─────────────────────────────────────────────────────────

    public function test_requires_api_key(): void
    {
        $url = (getenv('APP_URL') ?: 'http://localhost:8001') . '/api/v1/posts';
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer bad_token'],
        ]);
        curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $this->assertSame(401, $status);
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function createPost(string $title = ''): string
    {
        $response = $this->post('/api/v1/posts', [
            'data' => ['type' => 'posts', 'attributes' => ['title' => ($title ?: uniqid()), 'body' => 'Test body']],
        ]);
        $id = $response->json['data']['id'];
        $this->created[] = $id;
        return $id;
    }
}
