<?php

require_once APP_ROOT . '/app/controllers/Controller.php';
require_once APP_ROOT . '/app/views/components/post_card.php';
require_once APP_ROOT . '/app/views/components/pagination.php';

class BlogController extends Controller
{
    public function index(): string
    {
        $this->title = 'Blog';
        $size   = 12;
        $number = max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($number - 1) * $size;
        $total  = Post::count();
        $last   = max(1, (int) ceil($total / $size));
        $posts  = Post::paginate($size, $offset);
        return $this->render('blog/index', compact('posts', 'number', 'last', 'total'));
    }
}
