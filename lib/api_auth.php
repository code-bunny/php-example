<?php

// API keys use the standard Bearer token scheme — the same header format
// used by JWT and OAuth 2.0, so this pattern scales if you later swap
// to either of those.
//
// Usage:  Authorization: Bearer <your-api-key>

function api_authenticate(): void {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token  = str_starts_with($header, 'Bearer ') ? substr($header, 7) : '';

    if (!$token || !ApiKey::findByToken($token)) {
        header('Content-Type: application/vnd.api+json');
        http_response_code(401);
        echo json_encode([
            'jsonapi' => ['version' => '1.1'],
            'errors'  => [['status' => '401', 'title' => 'Unauthorized. Provide a valid Bearer token.']],
        ], JSON_UNESCAPED_SLASHES);
        exit;
    }
}
