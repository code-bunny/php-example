<?php

// Load .env for CLI scripts (migrate.php, seeds.php).
// When called from index.php the env is already loaded.
if (empty($_ENV['DB_HOST'])) {
    require_once __DIR__ . '/helpers/env.php';
    load_env(__DIR__ . '/.env');
}

$pdo = new PDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
