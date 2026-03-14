<?php namespace Tests\Pages;

class HomeTest extends \PageTestCase
{
    public function test_home_page_returns_ok(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_home_page_shows_site_name(): void
    {
        $this->get('/')->assertOk()->assertSee("Let's PHP!");
    }
}
