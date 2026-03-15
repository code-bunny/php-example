<?php

// Load .env for CLI scripts (migrate.php, seeds.php).
// When called from index.php the env is already loaded.
if (empty($_ENV['DB_HOST'])) {
    require_once __DIR__ . '/helpers/env.php';
    load_env(__DIR__ . '/.env');
}

require_once __DIR__ . '/helpers/logger.php';

// Follow the Rails convention: mydb_development / mydb_test
$appEnv = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'development';
$dbName = $_ENV['DB_NAME'] . '_' . $appEnv;

$pdo = new LoggingPDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $dbName,
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
