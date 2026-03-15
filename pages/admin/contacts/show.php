<?php

require_once __DIR__ . '/../../../db.php';
require_once __DIR__ . '/../_nav.php';
Model::setDb($pdo);

$contact = Contact::find($id);
$title   = 'Contact';

if (!$contact) {
    http_response_code(404);
    echo '<p class="text-gray-500">Contact not found.</p>';
    return;
}

?>

<?php admin_nav('contacts') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Contact</h1>
    <a href="/admin/contacts" class="text-sm text-gray-500 hover:text-gray-900">← Back to Contacts</a>
</div>

<div class="bg-white border border-gray-200 rounded-lg p-6 max-w-2xl space-y-4">
    <div>
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Email</p>
        <p class="text-sm text-gray-900"><?= htmlspecialchars($contact->email) ?></p>
    </div>
    <div>
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Received</p>
        <p class="text-sm text-gray-500"><?= htmlspecialchars($contact->created_at) ?></p>
    </div>
    <div>
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Message</p>
        <p class="text-sm text-gray-900 whitespace-pre-wrap"><?= htmlspecialchars($contact->message) ?></p>
    </div>
    <div class="pt-2">
        <form method="POST" action="/admin/contacts/<?= $contact->id ?>/delete"
              onsubmit="return confirm('Delete this message?')">
            <?php csrf_field() ?>
            <button type="submit" class="text-sm text-red-600 hover:underline">Delete this message</button>
        </form>
    </div>
</div>
