<?php

require_once 'models/Post.php';

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// API routes — respond directly, no layout
if ($path === '/api/v1/posts') {
    require 'pages/api/v1/posts.php';
    exit;
}

if (preg_match('#^/api/v1/posts/(\d+)$#', $path, $matches)) {
    $id = (int) $matches[1];
    require 'pages/api/v1/posts/show.php';
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
        http_response_code(404);
        $title = '404';
        echo '<h1 class="text-2xl font-bold">Page not found</h1>';
}

$content = ob_get_clean();
require 'layout.php';
