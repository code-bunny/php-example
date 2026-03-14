<?php

function up(PDO $pdo): void {
    // Add uuid column if it doesn't already exist
    $columns = $pdo->query("SHOW COLUMNS FROM posts LIKE 'uuid'")->fetchAll();
    if (empty($columns)) {
        $pdo->exec("ALTER TABLE posts ADD COLUMN uuid CHAR(36) NOT NULL DEFAULT ''");
    }

    // Populate any rows that don't have a uuid yet
    $pdo->exec("UPDATE posts SET uuid = UUID() WHERE uuid = ''");

    // Swap out the primary key if id is still an integer
    $idColumn = $pdo->query("SHOW COLUMNS FROM posts LIKE 'id'")->fetch(PDO::FETCH_ASSOC);
    if ($idColumn && str_contains(strtolower($idColumn['Type']), 'int')) {
        $pdo->exec("ALTER TABLE posts MODIFY COLUMN id INT NOT NULL");
        $pdo->exec("ALTER TABLE posts DROP PRIMARY KEY");
        $pdo->exec("ALTER TABLE posts DROP COLUMN id");
        $pdo->exec("ALTER TABLE posts RENAME COLUMN uuid TO id");
        $pdo->exec("ALTER TABLE posts MODIFY COLUMN id CHAR(36) NOT NULL FIRST");
        $pdo->exec("ALTER TABLE posts ADD PRIMARY KEY (id)");
    }
}
