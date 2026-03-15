<?php

// When bin/coverage starts the server with XDEBUG_MODE=coverage, collect
// per-request coverage data and write it to a temp file for later merging.
$xdebugMode = getenv('XDEBUG_MODE') ?: ini_get('xdebug.mode');
if (function_exists('xdebug_start_code_coverage') && str_contains($xdebugMode, 'coverage')) {
    xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
    register_shutdown_function(function () {
        file_put_contents(
            sys_get_temp_dir() . '/phpcov_request_' . uniqid() . '.json',
            json_encode(xdebug_get_code_coverage()),
        );
    });
}

require_once __DIR__ . '/lib/env.php';
load_env(__DIR__ . '/.env');

define('APP_ROOT', __DIR__);

require_once APP_ROOT . '/lib/logger.php';
start_request_log();

// Show errors in development; log silently in production
error_reporting(E_ALL);
if (($_ENV['APP_ENV'] ?? 'development') === 'production') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
} else {
    ini_set('display_errors', '1');
}

require_once APP_ROOT . '/app/models/Post.php';
require_once APP_ROOT . '/app/models/Contact.php';
require_once APP_ROOT . '/app/models/Subscriber.php';
require_once APP_ROOT . '/app/models/ApiKey.php';
require_once APP_ROOT . '/lib/csrf.php';
require_once APP_ROOT . '/lib/flash.php';
require_once APP_ROOT . '/lib/rate_limit.php';
csrf_start();

require_once APP_ROOT . '/config/database.php';
Model::setDb($pdo);

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Security headers for all HTML responses (set early; API routes may override)
if (!str_starts_with($path, '/api/') && $path !== '/openapi.yaml') {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://esm.sh https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; img-src 'self' data: https://placecats.com; frame-src https://maps.google.com https://www.google.com; connect-src 'self' https://maps.googleapis.com; worker-src blob:");
}

// OpenAPI spec
if ($path === '/openapi.yaml') {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/yaml');
    readfile(__DIR__ . '/config/openapi.yaml');
    exit;
}

// CORS for API routes
if (str_starts_with($path, '/api/')) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit;
    }

    require_once APP_ROOT . '/lib/api_auth.php';
    api_authenticate();
    require_once APP_ROOT . '/app/api/Route.php';
    Route::prefix('/api/v1');
    require_once APP_ROOT . '/app/api/Api.php';

    Route::dispatch($_SERVER['REQUEST_METHOD'], $path) || notFound();
    exit;
}

require APP_ROOT . '/config/routes.php';
