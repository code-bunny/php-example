<?php

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
