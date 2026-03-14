<?php

function admin_auth(): void {
    $user = $_SERVER['PHP_AUTH_USER'] ?? '';
    $pass = $_SERVER['PHP_AUTH_PW']   ?? '';

    // In production, use environment variables:
    // $user !== getenv('ADMIN_USER') || $pass !== getenv('ADMIN_PASS')
    if ($user !== 'admin' || $pass !== 'secret') {
        header('WWW-Authenticate: Basic realm="Admin"');
        http_response_code(401);
        exit('Unauthorized');
    }
}
