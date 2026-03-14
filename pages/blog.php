<?php

require_once __DIR__ . '/components/post_card.php';

require_once __DIR__ . '/../db.php';
Model::setDb($pdo);

$size   = 12;
$number = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($number - 1) * $size;
$total  = Post::count();
$last   = max(1, (int) ceil($total / $size));
$posts  = Post::paginate($size, $offset);

$title = 'Blog';

// Build page window: always show first, last, and up to 3 pages around current
$pages = array_unique(array_filter(array_merge(
    [1, 2],
    [$number - 1, $number, $number + 1],
    [$last - 1, $last]
), fn($p) => $p >= 1 && $p <= $last));
sort($pages);

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

<?php if ($last > 1): ?>
    <nav class="flex justify-center items-center gap-1 mt-12">

        <a href="/blog?page=<?= max(1, $number - 1) ?>"
           class="px-3 py-2 rounded text-sm <?= $number === 1 ? 'text-gray-300 pointer-events-none' : 'text-indigo-500 hover:bg-indigo-50' ?>">
            &laquo;
        </a>

        <?php $prev = null; foreach ($pages as $p): ?>
            <?php if ($prev !== null && $p - $prev > 1): ?>
                <span class="px-2 py-2 text-sm text-gray-400">&hellip;</span>
            <?php endif; ?>
            <a href="/blog?page=<?= $p ?>"
               class="px-3 py-2 rounded text-sm <?= $p === $number ? 'bg-indigo-500 text-white' : 'text-indigo-500 hover:bg-indigo-50' ?>">
                <?= $p ?>
            </a>
            <?php $prev = $p; endforeach; ?>

        <a href="/blog?page=<?= min($last, $number + 1) ?>"
           class="px-3 py-2 rounded text-sm <?= $number === $last ? 'text-gray-300 pointer-events-none' : 'text-indigo-500 hover:bg-indigo-50' ?>">
            &raquo;
        </a>

    </nav>
    <p class="text-center text-sm text-gray-400 mt-2">Page <?= $number ?> of <?= $last ?> &mdash; <?= $total ?> posts</p>
<?php endif; ?>
