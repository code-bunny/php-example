<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=mydb', 'user', 'pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

// Ensure migrations tracking table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    run_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$ran = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

$files = glob(__DIR__ . '/migrations/*.php');
sort($files);

$pending = array_filter($files, fn($f) => !in_array(basename($f), $ran));

if (empty($pending)) {
    echo "Nothing to migrate.\n";
    exit;
}

foreach ($pending as $file) {
    $name = basename($file);
    echo "Running: $name ... ";
    require $file;
    up($pdo);
    $pdo->prepare("INSERT INTO migrations (migration) VALUES (?)")->execute([$name]);
    echo "done\n";
}
