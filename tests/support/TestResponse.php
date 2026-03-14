<?php

use PHPUnit\Framework\Assert;

class TestResponse
{
    public int    $status;
    public string $body;
    public ?array $json;

    public function __construct(int $status, string $body)
    {
        $this->status = $status;
        $this->body   = $body;
        $this->json   = json_decode($body, true);
    }

    // Fluent assertions so you can chain: $response->assertStatus(200)->assertJsonPath('data.0.type', 'posts')

    public function assertStatus(int $expected): static
    {
        Assert::assertSame($expected, $this->status, "Expected HTTP $expected, got $this->status.\n$this->body");
        return $this;
    }

    public function assertOk(): static       { return $this->assertStatus(200); }
    public function assertCreated(): static  { return $this->assertStatus(201); }
    public function assertNoContent(): static { return $this->assertStatus(204); }

    // Dot-notation path: assertJsonPath('data.0.attributes.title', 'Hello')
    public function assertJsonPath(string $path, mixed $expected): static
    {
        $actual = $this->jsonPath($path);
        Assert::assertSame($expected, $actual, "JSON path \"$path\" mismatch.\n$this->body");
        return $this;
    }

    public function assertJsonHas(string $path): static
    {
        Assert::assertNotNull($this->jsonPath($path), "JSON path \"$path\" not found.\n$this->body");
        return $this;
    }

    private function jsonPath(string $path): mixed
    {
        $node = $this->json;
        foreach (explode('.', $path) as $key) {
            if (!is_array($node) || !array_key_exists($key, $node)) return null;
            $node = $node[$key];
        }
        return $node;
    }
}
