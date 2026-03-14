<?php

require_once __DIR__ . '/../components/alert.php';
require_once __DIR__ . '/../components/form_input.php';
require_once __DIR__ . '/../components/form_textarea.php';

require_once __DIR__ . '/../../db.php';
Model::setDb($pdo);

$title = 'New Post';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_title = trim($_POST['title'] ?? '');
    $body       = trim($_POST['body'] ?? '');

    if ($post_title === '') {
        $errors[] = 'Title is required.';
    }

    if ($body === '') {
        $errors[] = 'Body is required.';
    }

    if (empty($errors)) {
        $post = new Post(['title' => $post_title, 'body' => $body]);
        $post->save();
        header('Location: /');
        exit;
    }
}

?>

<h1 class="text-2xl font-bold mb-6">New Post</h1>

<?php alert($errors) ?>

<form method="POST" action="/posts/new" class="space-y-4 bg-white border border-gray-200 rounded-lg p-6">
    <?php form_input('title', 'Title', $_POST['title'] ?? '') ?>
    <?php form_textarea('body', 'Body', $_POST['body'] ?? '') ?>
    <button type="submit"
            class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700">
        Create Post
    </button>
</form>
