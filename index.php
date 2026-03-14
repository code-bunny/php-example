<?php

require_once 'models/Post.php';
require_once 'models/Contact.php';
require_once 'models/Subscriber.php';
require_once 'helpers/csrf.php';
require_once 'helpers/rate_limit.php';

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Security headers for all HTML responses (set early; API routes may override)
if (!str_starts_with($path, '/api/') && $path !== '/openapi.yaml') {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://esm.sh; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; img-src 'self' data: https:; frame-src https://maps.google.com https://www.google.com; connect-src 'self' https://maps.googleapis.com");
}

// OpenAPI spec
if ($path === '/openapi.yaml') {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/yaml');
    readfile(__DIR__ . '/openapi.yaml');
    exit;
}

// CORS for API routes
if (str_starts_with($path, '/api/')) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit;
    }
}

// API routes — respond directly, no layout
if ($path === '/api/v1/posts') {
    require 'pages/api/v1/posts.php';
    exit;
}

if ($path === '/api/v1/contacts') {
    require 'pages/api/v1/contacts.php';
    exit;
}

if ($path === '/api/v1/subscribers') {
    require 'pages/api/v1/subscribers.php';
    exit;
}

if (preg_match('#^/api/v1/posts/([0-9a-f-]{36})$#', $path, $matches)) {
    $id = $matches[1];
    require 'pages/api/v1/posts/show.php';
    exit;
}

// Catch-all for unmatched /api routes
if ($path === '/api' || str_starts_with($path, '/api/')) {
    require_once 'pages/api/serialize.php';
    header('Content-Type: application/vnd.api+json');
    http_response_code(404);
    echo jsonapi_encode(['errors' => [['status' => '404', 'title' => 'Not found.']]]);
    exit;
}

// HTML routes
ob_start();

switch ($path) {
    case '/':
        require 'pages/home.php';
        break;

    case '/blog':
        require 'pages/blog.php';
        break;

    case '/about':
        require 'pages/about.php';
        break;

    case '/contact':
        require 'pages/contact.php';
        break;

    case '/subscribe':
        require 'pages/subscribe.php';
        break;

    case '/posts/new':
        require 'pages/posts/new.php';
        break;

    default:
        if (preg_match('#^/posts/([0-9a-f-]{36})$#', $path, $matches)) {
            $id = $matches[1];
            require 'pages/posts/show.php';
        } else {
            http_response_code(404);
            $title = '404';
            echo '<h1 class="text-2xl font-bold">Page not found</h1>';
        }
}

$content = ob_get_clean();
require 'layout.php';
