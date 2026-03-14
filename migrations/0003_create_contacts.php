<?php

function up(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS contacts (
        id CHAR(36) PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}
