<?php

require_once __DIR__ . '/../../../db.php';
require_once __DIR__ . '/../_nav.php';
require_once __DIR__ . '/../../components/form_input.php';
require_once __DIR__ . '/../../components/form_textarea.php';
require_once __DIR__ . '/../../components/alert.php';
Model::setDb($pdo);

// $id is set by the router when editing an existing post
$post   = isset($id) ? Post::find($id) : null;
$errors = [];
$title  = $post ? 'Edit Post' : 'New Post';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $post_title = trim($_POST['title'] ?? '');
    $body       = trim($_POST['body']  ?? '');

    if ($post_title === '') $errors[] = 'Title is required.';
    if ($body === '')       $errors[] = 'Body is required.';

    if (empty($errors)) {
        if ($post) {
            $post->title = $post_title;
            $post->body  = $body;
        } else {
            $post = new Post(['title' => $post_title, 'body' => $body]);
        }
        $post->save();
        header('Location: /admin/posts');
        exit;
    }

    http_response_code(422);
}

$post_title = $_POST['title'] ?? $post?->title ?? '';
$body       = $_POST['body']  ?? $post?->body  ?? '';

?>

<?php admin_nav('posts') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold"><?= $title ?></h1>
    <a href="/admin/posts" class="text-sm text-gray-500 hover:text-gray-900">← Back to Posts</a>
</div>

<?php alert($errors) ?>

<form method="POST" class="space-y-4 bg-white border border-gray-200 rounded-lg p-6 max-w-2xl">
    <?php csrf_field() ?>
    <?php form_input('title', 'Title', $post_title) ?>
    <?php form_textarea('body', 'Body', $body) ?>
    <button type="submit" class="bg-indigo-500 text-white text-sm px-4 py-2 rounded hover:bg-indigo-600">
        <?= $post ? 'Update Post' : 'Create Post' ?>
    </button>
</form>
