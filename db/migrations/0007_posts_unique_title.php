<?php

return function (PDO $pdo): void {
    // Remove duplicate titles (keep the earliest) before adding the constraint
    $pdo->exec("DELETE p1 FROM posts p1
                INNER JOIN posts p2
                WHERE p1.created_at > p2.created_at AND p1.title = p2.title");

    $pdo->exec("ALTER TABLE posts ADD CONSTRAINT posts_title_unique UNIQUE (title)");
};
