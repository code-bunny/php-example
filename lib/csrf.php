<?php

function csrf_start(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function csrf_token(): string {
    csrf_start();
    return $_SESSION['csrf_token'];
}

function csrf_field(): void { ?>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
<?php }

function csrf_verify(): void {
    csrf_start();
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }
}
