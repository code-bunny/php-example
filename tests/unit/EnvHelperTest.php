<?php namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class EnvHelperTest extends TestCase
{
    private string $tmpFile;

    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../../lib/env.php';
    }

    protected function setUp(): void
    {
        $this->tmpFile = tempnam(sys_get_temp_dir(), 'phptest_env_');
    }

    protected function tearDown(): void
    {
        unlink($this->tmpFile);
    }

    private function write(string $contents): void
    {
        file_put_contents($this->tmpFile, $contents);
        load_env($this->tmpFile);
    }

    public function test_loads_simple_key_value(): void
    {
        $this->write("UNIT_TEST_KEY=hello\n");

        $this->assertSame('hello', $_ENV['UNIT_TEST_KEY']);
    }

    public function test_strips_quoted_values(): void
    {
        $this->write("UNIT_TEST_QUOTED=\"quoted value\"\n");

        $this->assertSame('quoted value', $_ENV['UNIT_TEST_QUOTED']);
    }

    public function test_ignores_comment_lines(): void
    {
        $this->write("# This is a comment\nUNIT_TEST_AFTER_COMMENT=yes\n");

        $this->assertSame('yes', $_ENV['UNIT_TEST_AFTER_COMMENT']);
        $this->assertArrayNotHasKey('# This is a comment', $_ENV);
    }

    public function test_ignores_blank_lines(): void
    {
        $this->write("\n\nUNIT_TEST_BLANK=ok\n\n");

        $this->assertSame('ok', $_ENV['UNIT_TEST_BLANK']);
    }
}
