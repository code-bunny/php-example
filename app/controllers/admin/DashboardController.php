<?php

require_once APP_ROOT . '/app/controllers/admin/AdminController.php';
require_once APP_ROOT . '/app/views/admin/_nav.php';

class DashboardController extends AdminController
{
    public function index(): string
    {
        $this->title = 'Admin';
        $stats = [
            ['label' => 'Posts',       'count' => Post::count(),       'href' => '/admin/posts'],
            ['label' => 'Contacts',    'count' => Contact::count(),    'href' => '/admin/contacts'],
            ['label' => 'Subscribers', 'count' => Subscriber::count(), 'href' => '/admin/subscribers'],
        ];
        return $this->render('admin/dashboard/index', ['stats' => $stats]);
    }
}
