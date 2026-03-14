<?php

$pdo = new PDO('mysql:host=127.0.0.1;dbname=mydb', 'user', 'pass');
Model::setDb($pdo);
$posts = Post::all();

$title = 'Home';

?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Posts</h1>
</div>

<?php if (empty($posts)): ?>
    <p class="text-gray-500">No posts yet. <a href="/posts/new" class="text-blue-600 hover:underline">Create one.</a></p>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($posts as $post): ?>
            <article class="bg-white border border-gray-200 rounded-lg p-5">
                <h2 class="text-lg font-semibold mb-1"><?= htmlspecialchars($post->title) ?></h2>
                <div class="text-gray-700 text-sm"><?= $post->body ?></div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
