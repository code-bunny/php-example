<?php

require_once __DIR__ . '/../../../models/User.php';
require_once __DIR__ . '/../_nav.php';

$title = 'Admin — Users';
$users = User::all();

?>

<?php admin_nav('users') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Users</h1>
    <a href="/admin/users/new" class="bg-indigo-500 text-white text-sm py-2 px-4 rounded hover:bg-indigo-600">New User</a>
</div>

<div class="overflow-x-auto">
    <table class="w-full text-sm text-left border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3">Created</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($users)): ?>
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No users yet.</td></tr>
            <?php endif ?>
            <?php foreach ($users as $user): ?>
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-gray-900"><?= htmlspecialchars($user->email) ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($user->role) ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($user->created_at) ?></td>
                    <td class="px-4 py-3 text-right space-x-3">
                        <a href="/admin/users/<?= $user->id ?>/edit" class="text-indigo-600 hover:underline">Edit</a>
                        <form method="POST" action="/admin/users/<?= $user->id ?>/delete" class="inline"
                              onsubmit="return confirm('Delete this user?')">
                            <?php csrf_field() ?>
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
