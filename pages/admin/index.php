<?php

require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/_nav.php';
Model::setDb($pdo);

$title = 'Admin';

$stats = [
    ['label' => 'Posts',       'count' => Post::count(),       'href' => '/admin/posts'],
    ['label' => 'Contacts',    'count' => Contact::count(),    'href' => '/admin/contacts'],
    ['label' => 'Subscribers', 'count' => Subscriber::count(), 'href' => '/admin/subscribers'],
];

?>

<?php admin_nav('dashboard') ?>

<h1 class="text-2xl font-bold mb-6">Admin</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <?php foreach ($stats as $stat): ?>
        <a href="<?= $stat['href'] ?>" class="block bg-white border border-gray-200 rounded-lg p-6 hover:border-indigo-400 transition-colors">
            <p class="text-3xl font-bold text-indigo-600"><?= $stat['count'] ?></p>
            <p class="text-sm text-gray-500 mt-1"><?= $stat['label'] ?></p>
        </a>
    <?php endforeach ?>
</div>
