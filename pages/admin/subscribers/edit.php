<?php

require_once __DIR__ . '/../../../db.php';
require_once __DIR__ . '/../_nav.php';
require_once __DIR__ . '/../../components/form_input.php';
require_once __DIR__ . '/../../components/alert.php';
Model::setDb($pdo);

// $id is set by the router when editing an existing subscriber
$subscriber = isset($id) ? Subscriber::find($id) : null;
$errors     = [];
$title      = $subscriber ? 'Edit Subscriber' : 'New Subscriber';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';

    if (empty($errors)) {
        if ($subscriber) {
            $subscriber->email = $email;
        } else {
            $subscriber = new Subscriber(['email' => $email]);
        }
        $subscriber->save();
        header('Location: /admin/subscribers');
        exit;
    }

    http_response_code(422);
}

$email = $_POST['email'] ?? $subscriber?->email ?? '';

?>

<?php admin_nav('subscribers') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold"><?= $title ?></h1>
    <a href="/admin/subscribers" class="text-sm text-gray-500 hover:text-gray-900">← Back to Subscribers</a>
</div>

<?php alert($errors) ?>

<form method="POST" class="space-y-4 bg-white border border-gray-200 rounded-lg p-6 max-w-2xl">
    <?php csrf_field() ?>
    <?php form_input('email', 'Email', $email) ?>
    <button type="submit" class="bg-indigo-500 text-white text-sm px-4 py-2 rounded hover:bg-indigo-600">
        <?= $subscriber ? 'Update Subscriber' : 'Add Subscriber' ?>
    </button>
</form>
