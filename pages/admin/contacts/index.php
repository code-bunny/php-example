<?php

require_once __DIR__ . '/../../../db.php';
require_once __DIR__ . '/../_nav.php';
require_once __DIR__ . '/../../components/pagination.php';
require_once __DIR__ . '/../../components/icons.php';
Model::setDb($pdo);

$title  = 'Admin — Contacts';
$size   = 20;
$number = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($number - 1) * $size;
$total  = Contact::count();
$last   = max(1, (int) ceil($total / $size));
$contacts = Contact::paginate($size, $offset);

?>

<?php admin_nav('contacts') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Contacts</h1>
</div>

<div class="overflow-x-auto">
    <table class="w-full text-sm text-left border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Message</th>
                <th class="px-4 py-3">Created</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($contacts)): ?>
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No contacts yet.</td></tr>
            <?php endif ?>
            <?php foreach ($contacts as $contact): ?>
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-gray-900"><?= htmlspecialchars($contact->email) ?></td>
                    <td class="px-4 py-3 text-gray-600 max-w-xs truncate"><?= htmlspecialchars($contact->message) ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($contact->created_at) ?></td>
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-3">
                            <a href="/admin/contacts/<?= $contact->id ?>" class="inline-flex text-indigo-600 hover:text-indigo-800" title="View"><?= icon('eye') ?></a>
                            <form method="POST" action="/admin/contacts/<?= $contact->id ?>/delete" class="inline-flex"
                                  onsubmit="return confirm('Delete this message?')">
                                <?php csrf_field() ?>
                                <button type="submit" class="inline-flex p-0 text-red-500 hover:text-red-700 leading-none" title="Delete"><?= icon('trash') ?></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?php pagination($number, $last, $total, '/admin/contacts') ?>
