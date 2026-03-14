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
    header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit;
    }

    require_once 'db.php';
    Model::setDb($pdo);
    require_once 'pages/api/ApiResource.php';
    require_once 'pages/api/v1/PostsResource.php';
    require_once 'pages/api/v1/ContactsResource.php';
    require_once 'pages/api/v1/SubscribersResource.php';
}

// API routes — respond directly, no layout
if ($path === '/api/v1/posts') {
    (new PostsResource())->collection(); exit;
}

if ($path === '/api/v1/contacts') {
    (new ContactsResource())->collection(); exit;
}

if ($path === '/api/v1/subscribers') {
    (new SubscribersResource())->collection(); exit;
}

if (preg_match('#^/api/v1/posts/([0-9a-f-]{36})$#', $path, $matches)) {
    (new PostsResource())->member($matches[1]); exit;
}

if (preg_match('#^/api/v1/contacts/([0-9a-f-]{36})$#', $path, $matches)) {
    (new ContactsResource())->member($matches[1]); exit;
}

if (preg_match('#^/api/v1/subscribers/([0-9a-f-]{36})$#', $path, $matches)) {
    (new SubscribersResource())->member($matches[1]); exit;
}

// Catch-all for unmatched /api routes
if ($path === '/api' || str_starts_with($path, '/api/')) {
    header('Content-Type: application/vnd.api+json');
    http_response_code(404);
    echo json_encode(['jsonapi' => ['version' => '1.1'], 'errors' => [['status' => '404', 'title' => 'Not found.']]], JSON_UNESCAPED_SLASHES);
    exit;
}

// Admin routes
if ($path === '/admin' || str_starts_with($path, '/admin/')) {
    require_once 'helpers/admin_auth.php';
    admin_auth();
    require_once 'db.php';
    Model::setDb($pdo);

    // Deletes: POST /admin/{resource}/{id}/delete → delete record, redirect to list
    if (preg_match('#^/admin/(posts|contacts|subscribers)/([0-9a-f-]{36})/delete$#', $path, $m)
        && $_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $class  = match($m[1]) { 'posts' => 'Post', 'contacts' => 'Contact', 'subscribers' => 'Subscriber' };
        $record = $class::find($m[2]);
        if ($record) $record->delete();
        header('Location: /admin/' . $m[1]);
        exit;
    }

    ob_start();

    if ($path === '/admin') {
        require 'pages/admin/index.php';
    } elseif ($path === '/admin/posts') {
        require 'pages/admin/posts/index.php';
    } elseif ($path === '/admin/posts/new') {
        require 'pages/admin/posts/edit.php';
    } elseif (preg_match('#^/admin/posts/([0-9a-f-]{36})/edit$#', $path, $matches)) {
        $id = $matches[1];
        require 'pages/admin/posts/edit.php';
    } elseif ($path === '/admin/contacts') {
        require 'pages/admin/contacts/index.php';
    } elseif (preg_match('#^/admin/contacts/([0-9a-f-]{36})/edit$#', $path, $matches)) {
        $id = $matches[1];
        require 'pages/admin/contacts/edit.php';
    } elseif ($path === '/admin/subscribers') {
        require 'pages/admin/subscribers/index.php';
    } elseif ($path === '/admin/subscribers/new') {
        require 'pages/admin/subscribers/edit.php';
    } elseif (preg_match('#^/admin/subscribers/([0-9a-f-]{36})/edit$#', $path, $matches)) {
        $id = $matches[1];
        require 'pages/admin/subscribers/edit.php';
    } else {
        http_response_code(404);
        $title = '404';
        echo '<h1 class="text-2xl font-bold">Admin page not found</h1>';
    }

    $content = ob_get_clean();
    require 'layout.php';
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
