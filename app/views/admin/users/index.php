<?php admin_nav('users') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Users</h1>
    <a href="/admin/users/new" class="inline-flex items-center gap-1.5 bg-indigo-500 text-white text-sm py-2 px-4 rounded hover:bg-indigo-600">
        <?= icon('user-plus', 'w-4 h-4') ?> New User
    </a>
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
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-3">
                            <a href="/admin/users/<?= $user->id ?>/edit" class="inline-flex text-indigo-600 hover:text-indigo-800" title="Edit"><?= icon('pencil-square') ?></a>
                            <form method="POST" action="/admin/users/<?= $user->id ?>/delete" class="inline-flex"
                                  onsubmit="return confirm('Delete this user?')">
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
