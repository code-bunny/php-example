<?php

require_once 'models/Post.php';

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// API routes — respond directly, no layout
if ($path === '/api/v1/posts') {
    require 'pages/api/v1/posts.php';
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

    case '/about':
        require 'pages/about.php';
        break;

    case '/contact':
        require 'pages/contact.php';
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
