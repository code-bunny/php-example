<?php

return function (PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS api_keys (
        id         CHAR(36)     PRIMARY KEY,
        name       VARCHAR(255) NOT NULL,
        token      CHAR(64)     NOT NULL UNIQUE,
        created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    )");
};
