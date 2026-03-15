<?php

require_once APP_ROOT . '/app/controllers/admin/AdminController.php';
require_once APP_ROOT . '/app/views/admin/_nav.php';
require_once APP_ROOT . '/app/views/components/pagination.php';
require_once APP_ROOT . '/app/views/components/icons.php';

class AdminPostsController extends AdminController
{
    public function index(): string
    {
        $this->title = 'Admin — Posts';
        $size   = 20;
        $number = max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($number - 1) * $size;
        $total  = Post::count();
        $last   = max(1, (int) ceil($total / $size));
        $posts  = Post::paginate($size, $offset);
        return $this->render('admin/posts/index', compact('posts', 'number', 'last', 'total'));
    }

    public function create(): string
    {
        $this->title = 'New Post';
        return $this->form(null);
    }

    public function edit(string $id): string
    {
        $post = Post::find($id);
        if (!$post) {
            http_response_code(404);
            $this->title = '404';
            return '<p class="text-gray-500">Post not found.</p>';
        }
        $this->title = 'Edit Post';
        return $this->form($post);
    }

    private function form(?Post $post): string
    {
        require_once APP_ROOT . '/app/views/components/form_input.php';
        require_once APP_ROOT . '/app/views/components/form_textarea.php';
        require_once APP_ROOT . '/app/views/components/alert.php';

        $errors     = [];
        $post_title = $_POST['title'] ?? $post?->title ?? '';
        $body       = $_POST['body']  ?? $post?->body  ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();

            $post_title = trim($_POST['title'] ?? '');
            $body       = trim($_POST['body']  ?? '');

            if ($post_title === '') $errors[] = 'Title is required.';
            if ($body === '')       $errors[] = 'Body is required.';

            if ($post_title && empty($errors)) {
                $existing = Post::where('title', $post_title);
                if (!empty($existing) && $existing[0]->id !== ($post?->id)) {
                    $errors[] = 'A post with that title already exists.';
                }
            }

            if (empty($errors)) {
                $is_new = ($post === null);
                if ($post) {
                    $post->title = $post_title;
                    $post->body  = $body;
                } else {
                    $post = new Post(['title' => $post_title, 'body' => $body]);
                }
                $post->save();
                flash($is_new ? 'Post created.' : 'Post updated.');
                if ($is_new) {
                    $_SESSION['flash_confetti'] = true;
                }
                $this->redirect('/admin/posts');
            }

            http_response_code(422);
        }

        return $this->render('admin/posts/edit', compact('post', 'post_title', 'body', 'errors'));
    }
}
