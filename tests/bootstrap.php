<?php

// Force test environment before loading .env so load_env() won't overwrite it
$_ENV['APP_ENV'] = 'test';
putenv('APP_ENV=test');

// Load environment variables and helpers
require_once __DIR__ . '/../helpers/env.php';
load_env(__DIR__ . '/../.env');

require_once __DIR__ . '/../helpers/logger.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../models/ApiKey.php';
Model::setDb($pdo);

// Load test base classes
require_once __DIR__ . '/support/TestResponse.php';
require_once __DIR__ . '/support/TestCase.php';
require_once __DIR__ . '/support/PageResponse.php';
require_once __DIR__ . '/support/PageTestCase.php';
require_once __DIR__ . '/support/AdminTestCase.php';

// ── Test server ───────────────────────────────────────────────────────────────
// Start one automatically if nothing is listening on the test port, so tests
// work whether invoked via bin/test, bin/coverage, or directly via phpunit.
$testUrl = getenv('APP_URL') ?: 'http://localhost:8001';
$testPort = parse_url($testUrl, PHP_URL_PORT) ?? 8001;

$serverPid = null;
$sock = @fsockopen('127.0.0.1', $testPort, timeout: 0.1);
if ($sock) {
    fclose($sock);
} else {
    $root = dirname(__DIR__);
    $xdebugMode = getenv('XDEBUG_MODE') ?: ini_get('xdebug.mode');
    $cmd = str_contains($xdebugMode, 'coverage')
        ? "APP_ENV=test XDEBUG_MODE=coverage php -S localhost:$testPort $root/router.php"
        : "APP_ENV=test php -S localhost:$testPort $root/router.php";
    $serverPid = exec("$cmd >/dev/null 2>&1 & echo \$!", $out);
    usleep(800_000);

    register_shutdown_function(function () use ($serverPid) {
        posix_kill((int) $serverPid, SIGTERM);
    });
}

// ── Temporary API key for the test suite ─────────────────────────────────────
define('TEST_API_KEY', 'test_' . bin2hex(random_bytes(16)));

(new ApiKey(['name' => 'Test Suite', 'token' => TEST_API_KEY]))->save();

register_shutdown_function(function () use ($pdo) {
    $pdo->prepare("DELETE FROM api_keys WHERE token = ?")->execute([TEST_API_KEY]);
});

// ── In-process coverage (unit tests) when running under bin/coverage ──────────
$xdebugMode = getenv('XDEBUG_MODE') ?: ini_get('xdebug.mode');
if (function_exists('xdebug_start_code_coverage') && str_contains($xdebugMode, 'coverage')) {
    xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
    register_shutdown_function(function () {
        file_put_contents(
            sys_get_temp_dir() . '/phpcov_unit_' . getmypid() . '.json',
            json_encode(xdebug_get_code_coverage()),
        );
    });
}
