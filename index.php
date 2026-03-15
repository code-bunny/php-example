<?php

// When bin/coverage starts the server with XDEBUG_MODE=coverage, collect
// per-request coverage data and write it to a temp file for later merging.
$xdebugMode = getenv('XDEBUG_MODE') ?: ini_get('xdebug.mode');
if (function_exists('xdebug_start_code_coverage') && str_contains($xdebugMode, 'coverage')) {
    xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
    register_shutdown_function(function () {
        file_put_contents(
            sys_get_temp_dir() . '/phpcov_request_' . uniqid() . '.json',
            json_encode(xdebug_get_code_coverage()),
        );
    });
}

require_once __DIR__ . '/lib/env.php';
load_env(__DIR__ . '/.env');

define('APP_ROOT', __DIR__);

require_once APP_ROOT . '/lib/logger.php';
start_request_log();

// Show errors in development; log silently in production
error_reporting(E_ALL);
if (($_ENV['APP_ENV'] ?? 'development') === 'production') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
} else {
    ini_set('display_errors', '1');
}

require_once APP_ROOT . '/app/models/Post.php';
require_once APP_ROOT . '/app/models/Contact.php';
require_once APP_ROOT . '/app/models/Subscriber.php';
require_once APP_ROOT . '/app/models/ApiKey.php';
require_once APP_ROOT . '/lib/csrf.php';
require_once APP_ROOT . '/lib/flash.php';
require_once APP_ROOT . '/lib/rate_limit.php';
csrf_start();

require_once 'db.php';
Model::setDb($pdo);

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Security headers for all HTML responses (set early; API routes may override)
if (!str_starts_with($path, '/api/') && $path !== '/openapi.yaml') {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://esm.sh https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; img-src 'self' data: https://placecats.com; frame-src https://maps.google.com https://www.google.com; connect-src 'self' https://maps.googleapis.com; worker-src blob:");
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
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit;
    }

    require_once APP_ROOT . '/lib/api_auth.php';
    api_authenticate();
    require_once APP_ROOT . '/app/controllers/api/ApiResource.php';
    require_once APP_ROOT . '/app/controllers/api/v1/PostsResource.php';
    require_once APP_ROOT . '/app/controllers/api/v1/ContactsResource.php';
    require_once APP_ROOT . '/app/controllers/api/v1/SubscribersResource.php';
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
    require_once APP_ROOT . '/app/models/User.php';
    require_once APP_ROOT . '/lib/admin_auth.php';

    // Login — no auth required
    if ($path === '/admin/login') {
        require_once APP_ROOT . '/app/controllers/admin/LoginController.php';
        $ctrl    = new LoginController();
        $content = $ctrl->index();
        $title   = $ctrl->title;
        require APP_ROOT . '/app/views/layouts/admin.php';
        exit;
    }

    // Logout
    if ($path === '/admin/logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        session_destroy();
        header('Location: /admin/login');
        exit;
    }

    admin_require_auth();

    // Deletes: POST /admin/{resource}/{id}/delete → delete record, redirect to list
    if (preg_match('#^/admin/(posts|contacts|subscribers|api_keys|users)/([0-9a-f-]{36})/delete$#', $path, $m)
        && $_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_verify();
        $class  = match($m[1]) {
            'posts'       => 'Post',
            'contacts'    => 'Contact',
            'subscribers' => 'Subscriber',
            'api_keys'    => 'ApiKey',
            'users'       => 'User',
        };
        $record = $class::find($m[2]);
        if ($record) {
            $record->delete();
            $label = match($m[1]) {
                'posts'       => 'Post',
                'contacts'    => 'Contact',
                'subscribers' => 'Subscriber',
                'api_keys'    => 'API key',
                'users'       => 'User',
            };
            flash($label . ' deleted.');
        }
        header('Location: /admin/' . $m[1]);
        exit;
    }

    if ($path === '/admin') {
        require_once APP_ROOT . '/app/controllers/admin/DashboardController.php';
        $ctrl    = new DashboardController();
        $content = $ctrl->index();
    } elseif ($path === '/admin/posts') {
        require_once APP_ROOT . '/app/controllers/admin/AdminPostsController.php';
        $ctrl    = new AdminPostsController();
        $content = $ctrl->index();
    } elseif ($path === '/admin/posts/new') {
        require_once APP_ROOT . '/app/controllers/admin/AdminPostsController.php';
        $ctrl    = new AdminPostsController();
        $content = $ctrl->create();
    } elseif (preg_match('#^/admin/posts/([0-9a-f-]{36})/edit$#', $path, $matches)) {
        require_once APP_ROOT . '/app/controllers/admin/AdminPostsController.php';
        $ctrl    = new AdminPostsController();
        $content = $ctrl->edit($matches[1]);
    } elseif ($path === '/admin/contacts') {
        require_once APP_ROOT . '/app/controllers/admin/AdminContactsController.php';
        $ctrl    = new AdminContactsController();
        $content = $ctrl->index();
    } elseif (preg_match('#^/admin/contacts/([0-9a-f-]{36})$#', $path, $matches)) {
        require_once APP_ROOT . '/app/controllers/admin/AdminContactsController.php';
        $ctrl    = new AdminContactsController();
        $content = $ctrl->show($matches[1]);
    } elseif ($path === '/admin/subscribers') {
        require_once APP_ROOT . '/app/controllers/admin/AdminSubscribersController.php';
        $ctrl    = new AdminSubscribersController();
        $content = $ctrl->index();
    } elseif ($path === '/admin/api_keys') {
        require_once APP_ROOT . '/app/controllers/admin/AdminApiKeysController.php';
        $ctrl    = new AdminApiKeysController();
        $content = $ctrl->index();
    } elseif ($path === '/admin/users') {
        require_once APP_ROOT . '/app/controllers/admin/AdminUsersController.php';
        $ctrl    = new AdminUsersController();
        $content = $ctrl->index();
    } elseif ($path === '/admin/users/new') {
        require_once APP_ROOT . '/app/controllers/admin/AdminUsersController.php';
        $ctrl    = new AdminUsersController();
        $content = $ctrl->create();
    } elseif (preg_match('#^/admin/users/([0-9a-f-]{36})/edit$#', $path, $matches)) {
        require_once APP_ROOT . '/app/controllers/admin/AdminUsersController.php';
        $ctrl    = new AdminUsersController();
        $content = $ctrl->edit($matches[1]);
    } else {
        http_response_code(404);
        $ctrl    = new class { public string $title = '404'; };
        $content = '<h1 class="text-2xl font-bold">Admin page not found</h1>';
    }

    $title = $ctrl->title;
    require APP_ROOT . '/app/views/layouts/admin.php';
    exit;
}

// HTML routes
switch ($path) {
    case '/':
        require_once APP_ROOT . '/app/controllers/HomeController.php';
        $ctrl    = new HomeController();
        $content = $ctrl->index();
        break;

    case '/blog':
        require_once APP_ROOT . '/app/controllers/BlogController.php';
        $ctrl    = new BlogController();
        $content = $ctrl->index();
        break;

    case '/about':
        require_once APP_ROOT . '/app/controllers/AboutController.php';
        $ctrl    = new AboutController();
        $content = $ctrl->index();
        break;

    case '/contact':
        require_once APP_ROOT . '/app/controllers/ContactController.php';
        $ctrl    = new ContactController();
        $content = $ctrl->index();
        break;

    case '/subscribe':
        require_once APP_ROOT . '/app/controllers/SubscribeController.php';
        $ctrl = new SubscribeController();
        $ctrl->store(); // never returns
        break;

    default:
        if (preg_match('#^/posts/([0-9a-f-]{36})$#', $path, $matches)) {
            require_once APP_ROOT . '/app/controllers/PostsController.php';
            $ctrl    = new PostsController();
            $content = $ctrl->show($matches[1]);
        } else {
            http_response_code(404);
            $ctrl    = new class { public string $title = '404'; };
            $content = '<h1 class="text-2xl font-bold">Page not found</h1>';
        }
}

$title = $ctrl->title;
require APP_ROOT . '/app/views/layouts/public.php';
