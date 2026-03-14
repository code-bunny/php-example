<?php

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

// Create a temporary API key for the test suite and remove it when done
define('TEST_API_KEY', 'test_' . bin2hex(random_bytes(16)));

(new ApiKey(['name' => 'Test Suite', 'token' => TEST_API_KEY]))->save();

register_shutdown_function(function () use ($pdo) {
    $pdo->prepare("DELETE FROM api_keys WHERE token = ?")->execute([TEST_API_KEY]);
});
