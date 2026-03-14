<?php

require_once __DIR__ . '/components/post_card.php';
require_once __DIR__ . '/components/pagination.php';

require_once __DIR__ . '/../db.php';
Model::setDb($pdo);

$size   = 12;
$number = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($number - 1) * $size;
$total  = Post::count();
$last   = max(1, (int) ceil($total / $size));
$posts  = Post::paginate($size, $offset);

$title = 'Blog';

?>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-medium text-gray-900">Blog</h1>
</div>

<section class="text-gray-600 body-font overflow-hidden">
    <div class="flex flex-wrap -m-12">
        <?php if (empty($posts)): ?>
            <p class="text-gray-500 p-12">No posts yet.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?php post_card($post) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php pagination($number, $last, $total, '/blog') ?>
