<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=mydb', 'user', 'pass');
Model::setDb($pdo);

$title = 'New Post';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_title = trim($_POST['title'] ?? '');
    $body       = trim($_POST['body'] ?? '');

    if ($post_title === '') {
        $errors[] = 'Title is required.';
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

<?php if ($errors): ?>
    <div class="mb-4 bg-red-50 border border-red-200 rounded p-3 text-sm text-red-700 space-y-1">
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="/posts/new" class="space-y-4 bg-white border border-gray-200 rounded-lg p-6">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input type="text" id="title" name="title"
               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Body</label>
        <textarea id="body" name="body" rows="6"
                  class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($_POST['body'] ?? '') ?></textarea>
    </div>
    <button type="submit"
            class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700">
        Create Post
    </button>
</form>
