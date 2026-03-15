<?php

require_once APP_ROOT . '/app/controllers/admin/AdminController.php';
require_once APP_ROOT . '/app/views/admin/_nav.php';
require_once APP_ROOT . '/app/views/components/pagination.php';
require_once APP_ROOT . '/app/views/components/icons.php';

class AdminSubscribersController extends AdminController
{
    public function index(): string
    {
        $this->title = 'Admin — Subscribers';
        $size        = 20;
        $number      = max(1, (int) ($_GET['page'] ?? 1));
        $offset      = ($number - 1) * $size;
        $total       = Subscriber::count();
        $last        = max(1, (int) ceil($total / $size));
        $subscribers = Subscriber::paginate($size, $offset);
        return $this->render('admin/subscribers/index', compact('subscribers', 'number', 'last', 'total'));
    }
}
