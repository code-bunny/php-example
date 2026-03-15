<?php admin_nav('users') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold"><?= $user ? 'Edit User' : 'New User' ?></h1>
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
