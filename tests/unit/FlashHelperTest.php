<?php namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FlashHelperTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../../lib/flash.php';
    }

    protected function setUp(): void
    {
        // Give each test a clean session
        $_SESSION = [];
    }

    public function test_flash_stores_message_in_session(): void
    {
        flash('Record saved.');

        $this->assertSame('Record saved.', $_SESSION['flash']['message']);
        $this->assertSame('success', $_SESSION['flash']['type']);
    }

    public function test_flash_stores_custom_type(): void
    {
        flash('Something went wrong.', 'error');

        $this->assertSame('error', $_SESSION['flash']['type']);
    }

    public function test_flash_get_returns_the_message(): void
    {
        flash('Hello');

        $result = flash_get();

        $this->assertSame('Hello', $result['message']);
    }

    public function test_flash_get_clears_the_session(): void
    {
        flash('Hello');
        flash_get();

        $this->assertNull(flash_get());
    }

    public function test_flash_get_returns_null_when_nothing_set(): void
    {
        $this->assertNull(flash_get());
    }
}
