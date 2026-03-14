<?php

function up(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS subscribers (
        id CHAR(36) PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}
