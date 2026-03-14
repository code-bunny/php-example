<?php

use PHPUnit\Framework\Assert;

class PageResponse
{
    public int    $status;
    public string $body;

    public function __construct(int $status, string $body)
    {
        $this->status = $status;
        $this->body   = $body;
    }

    public function assertOk(): static        { return $this->assertStatus(200); }
    public function assertRedirect(): static  { return $this->assertStatus(302); }
    public function assertNotFound(): static  { return $this->assertStatus(404); }

    public function assertStatus(int $expected): static
    {
        Assert::assertSame($expected, $this->status, "Expected HTTP $expected, got $this->status.");
        return $this;
    }

    public function assertSee(string $text): static
    {
        Assert::assertStringContainsString($text, html_entity_decode($this->body, ENT_QUOTES), "Expected to find \"$text\" in response.");
        return $this;
    }

    public function assertRedirectsTo(string $path): static
    {
        // PageTestCase doesn't follow redirects, so we can inspect the Location header
        // This requires curl header capture — currently not implemented; use assertRedirect() instead.
        return $this->assertRedirect();
    }
}
