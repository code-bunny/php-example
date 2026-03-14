<?php

require_once __DIR__ . '/../../../db.php';
require_once __DIR__ . '/../_nav.php';
require_once __DIR__ . '/../../components/form_input.php';
require_once __DIR__ . '/../../components/form_textarea.php';
require_once __DIR__ . '/../../components/alert.php';
Model::setDb($pdo);

$contact = Contact::find($id);
$errors  = [];
$title   = 'Edit Contact';

if (!$contact) {
    http_response_code(404);
    echo '<p class="text-gray-500">Contact not found.</p>';
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email   = trim($_POST['email']   ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if ($message === '') $errors[] = 'Message is required.';

    if (empty($errors)) {
        $contact->email   = $email;
        $contact->message = $message;
        $contact->save();
        header('Location: /admin/contacts');
        exit;
    }

    http_response_code(422);
}

$email   = $_POST['email']   ?? $contact->email;
$message = $_POST['message'] ?? $contact->message;

?>

<?php admin_nav('contacts') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Edit Contact</h1>
    <a href="/admin/contacts" class="text-sm text-gray-500 hover:text-gray-900">← Back to Contacts</a>
</div>

<?php alert($errors) ?>

<form method="POST" class="space-y-4 bg-white border border-gray-200 rounded-lg p-6 max-w-2xl">
    <?php csrf_field() ?>
    <?php form_input('email', 'Email', $email) ?>
    <?php form_textarea('message', 'Message', $message) ?>
    <button type="submit" class="bg-indigo-500 text-white text-sm px-4 py-2 rounded hover:bg-indigo-600">
        Update Contact
    </button>
</form>
