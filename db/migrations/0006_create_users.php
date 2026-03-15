<?php

return function (PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id              CHAR(36)     PRIMARY KEY,
        email           VARCHAR(255) NOT NULL UNIQUE,
        password_digest VARCHAR(255) NOT NULL,
        role            VARCHAR(50)  NOT NULL DEFAULT 'admin',
        created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    )");
};
