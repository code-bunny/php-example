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
    <a href="/posts/new" class="inline-flex items-center bg-indigo-500 text-white border-0 py-2 px-4 focus:outline-none hover:bg-indigo-600 rounded">New Post
        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 ml-1" viewBox="0 0 24 24">
            <path d="M5 12h14M12 5l7 7-7 7"></path>
        </svg>
    </a>
</div>

<section class="text-gray-600 body-font overflow-hidden">
    <div class="flex flex-wrap -m-12">
        <?php if (empty($posts)): ?>
            <p class="text-gray-500 p-12">No posts yet. <a href="/posts/new" class="text-blue-600 hover:underline">Create one.</a></p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <?php post_card($post) ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php pagination($number, $last, $total, '/blog') ?>
