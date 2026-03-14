<?php

require_once __DIR__ . '/components/post_card.php';

require_once __DIR__ . '/../db.php';
Model::setDb($pdo);

$posts = Post::paginate(4, 0);
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
    <div class="text-center mt-12">
        <a href="/blog" class="inline-flex items-center text-indigo-500 hover:text-indigo-700">View all posts
            <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"></path>
                <path d="M12 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
<?php endif; ?>
