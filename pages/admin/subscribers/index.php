<?php

require_once __DIR__ . '/../../../db.php';
require_once __DIR__ . '/../_nav.php';
require_once __DIR__ . '/../../components/pagination.php';
Model::setDb($pdo);

$title  = 'Admin — Subscribers';
$size   = 20;
$number = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($number - 1) * $size;
$total  = Subscriber::count();
$last   = max(1, (int) ceil($total / $size));
$subscribers = Subscriber::paginate($size, $offset);

?>

<?php admin_nav('subscribers') ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold">Subscribers</h1>
</div>

<div class="overflow-x-auto">
    <table class="w-full text-sm text-left border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Subscribed</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($subscribers)): ?>
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">No subscribers yet.</td></tr>
            <?php endif ?>
            <?php foreach ($subscribers as $subscriber): ?>
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-gray-900"><?= htmlspecialchars($subscriber->email) ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($subscriber->created_at) ?></td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="/admin/subscribers/<?= $subscriber->id ?>/delete" class="inline"
                              onsubmit="return confirm('Unsubscribe this address?')">
                            <?php csrf_field() ?>
                            <button type="submit" class="text-red-600 hover:underline">Unsubscribe</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php pagination($number, $last, $total, '/admin/subscribers') ?>
