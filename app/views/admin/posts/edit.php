<?php admin_nav('posts') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold"><?= $post ? 'Edit Post' : 'New Post' ?></h1>
    <a href="/admin/posts" class="text-sm text-gray-500 hover:text-gray-900">← Back to Posts</a>
</div>

<?php alert($errors) ?>

<form method="POST" class="space-y-4 bg-white border border-gray-200 rounded-lg p-6 max-w-2xl">
    <?php csrf_field() ?>
    <?php form_input('title', 'Title', $post_title) ?>
    <?php form_textarea('body', 'Body', $body) ?>
    <button type="submit" class="bg-indigo-500 text-white text-sm px-4 py-2 rounded hover:bg-indigo-600">
        <?= $post ? 'Update Post' : 'Create Post' ?>
    </button>
</form>
