<?php

function admin_auth(): void {
    $user = $_SERVER['PHP_AUTH_USER'] ?? '';
    $pass = $_SERVER['PHP_AUTH_PW']   ?? '';

    if ($user !== $_ENV['ADMIN_USER'] || $pass !== $_ENV['ADMIN_PASS']) {
        header('WWW-Authenticate: Basic realm="Admin"');
        http_response_code(401);
        exit('Unauthorized');
    }
}
