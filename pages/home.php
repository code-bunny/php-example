<?php

require_once __DIR__ . '/components/post_card.php';

require_once __DIR__ . '/../db.php';
Model::setDb($pdo);

$posts = Post::all();
$title = 'Home';
?>

<?php if (empty($posts)): ?>
    <p class="text-gray-500">No posts yet. <a href="/posts/new" class="text-blue-600 hover:underline">Create one.</a></p>
<?php else: ?>
    <section class="text-gray-600 body-font overflow-hidden">
        <div class="flex flex-wrap -m-12">
            <?php foreach ($posts as $post): ?>
                <?php post_card($post) ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
