<?php

require_once APP_ROOT . '/app/controllers/Controller.php';

class PostsController extends Controller
{
    public function show(string $id): string
    {
        $post = Post::find($id);

        if (!$post) {
            http_response_code(404);
            $this->title = '404';
            return '<h1 class="text-2xl font-bold">Post not found</h1>';
        }

        $this->title = $post->title;
        return $this->render('posts/show', ['post' => $post]);
    }
}
