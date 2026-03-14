<?php

require_once __DIR__ . '/../../../db.php';
require_once __DIR__ . '/../_nav.php';
require_once __DIR__ . '/../../components/pagination.php';
Model::setDb($pdo);

$title  = 'Admin — Posts';
$size   = 20;
$number = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($number - 1) * $size;
$total  = Post::count();
$last   = max(1, (int) ceil($total / $size));
$posts  = Post::paginate($size, $offset);

?>

<?php admin_nav('posts') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Posts</h1>
    <a href="/admin/posts/new" class="bg-indigo-500 text-white text-sm py-2 px-4 rounded hover:bg-indigo-600">New Post</a>
</div>

<div class="overflow-x-auto">
    <table class="w-full text-sm text-left border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Created</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($posts)): ?>
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">No posts yet.</td></tr>
            <?php endif ?>
            <?php foreach ($posts as $post): ?>
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-gray-900"><?= htmlspecialchars($post->title) ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($post->created_at) ?></td>
                    <td class="px-4 py-3 text-right space-x-3">
                        <a href="/admin/posts/<?= $post->id ?>/edit" class="text-indigo-600 hover:underline">Edit</a>
                        <form method="POST" action="/admin/posts/<?= $post->id ?>/delete" class="inline"
                              onsubmit="return confirm('Delete this post?')">
                            <?php csrf_field() ?>
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php pagination($number, $last, $total, '/admin/posts') ?>
