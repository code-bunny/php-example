<?php namespace Tests\Pages;

class BlogTest extends \PageTestCase
{
    public function test_blog_page_returns_ok(): void
    {
        $this->get('/blog')->assertOk();
    }

    public function test_blog_page_shows_heading(): void
    {
        $this->get('/blog')->assertOk()->assertSee('Blog');
    }

    public function test_unknown_post_returns_404(): void
    {
        $this->get('/posts/00000000-0000-0000-0000-000000000000')->assertNotFound();
    }
}
