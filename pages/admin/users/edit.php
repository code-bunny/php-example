<?php

require_once __DIR__ . '/../../../models/User.php';
require_once __DIR__ . '/../_nav.php';
require_once __DIR__ . '/../../components/form_input.php';
require_once __DIR__ . '/../../components/alert.php';

// $id is set by the router when editing an existing user
$user   = isset($id) ? User::find($id) : null;
$errors = [];
$title  = $user ? 'Edit User' : 'New User';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role']     ?? 'admin');

    if ($email === '')                              $errors[] = 'Email is required.';
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email is invalid.';
    if (!$user && $password === '')                 $errors[] = 'Password is required.';
    if ($password !== '' && strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';

    if (empty($errors)) {
        if ($user) {
            $user->email = $email;
            $user->role  = $role;
            if ($password !== '') $user->setPassword($password);
        } else {
            $user = new User(['email' => $email, 'role' => $role]);
            $user->setPassword($password);
        }
        $user->save();
        flash(isset($id) ? 'User updated.' : 'User created.');
        header('Location: /admin/users');
        exit;
    }

    http_response_code(422);
}

$email = $_POST['email'] ?? $user?->email ?? '';
$role  = $_POST['role']  ?? $user?->role  ?? 'admin';

?>

<?php admin_nav('users') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold"><?= $title ?></h1>
    <a href="/admin/users" class="text-sm text-gray-500 hover:text-gray-900">← Back to Users</a>
</div>

<?php alert($errors) ?>

<form method="POST" class="space-y-4 bg-white border border-gray-200 rounded-lg p-6 max-w-md">
    <?php csrf_field() ?>
    <?php form_input('email', 'Email', $email) ?>
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            Password<?= $user ? ' <span class="text-gray-400 font-normal">(leave blank to keep current)</span>' : '' ?>
        </label>
        <input type="password" id="password" name="password" autocomplete="new-password"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
        <select id="role" name="role"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>admin</option>
        </select>
    </div>
    <button type="submit" class="bg-indigo-500 text-white text-sm px-4 py-2 rounded hover:bg-indigo-600">
        <?= $user ? 'Update User' : 'Create User' ?>
    </button>
</form>
