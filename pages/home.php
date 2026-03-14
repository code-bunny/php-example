<?php

require_once __DIR__ . '/components/post_card.php';

require_once __DIR__ . '/../db.php';
Model::setDb($pdo);

$posts = Post::all();
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Posts</h1>
</div>

<?php if (empty($posts)): ?>
    <p class="text-gray-500">No posts yet. <a href="/posts/new" class="text-blue-600 hover:underline">Create one.</a></p>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach ($posts as $post): ?>
            <?php post_card($post) ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
