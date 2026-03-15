<?php

require_once APP_ROOT . '/app/controllers/Controller.php';
require_once APP_ROOT . '/app/views/components/post_card.php';

class HomeController extends Controller
{
    public function index(): string
    {
        $this->title = 'Home';
        $posts = Post::paginate(4, 0);
        return $this->render('home/index', ['posts' => $posts]);
    }
}
