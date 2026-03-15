<?php

function admin_nav(string $current = ''): void { ?>
    <nav class="flex gap-6 mb-8 border-b border-gray-200 pb-4">
        <a href="/admin" class="text-sm font-medium <?= $current === 'dashboard'    ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-900' ?>">Dashboard</a>
        <a href="/admin/posts" class="text-sm font-medium <?= $current === 'posts'       ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-900' ?>">Posts</a>
        <a href="/admin/contacts" class="text-sm font-medium <?= $current === 'contacts'    ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-900' ?>">Contacts</a>
        <a href="/admin/subscribers" class="text-sm font-medium <?= $current === 'subscribers' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-900' ?>">Subscribers</a>
        <a href="/admin/api_keys" class="text-sm font-medium <?= $current === 'api_keys' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-900' ?>">API Keys</a>
        <a href="/admin/users" class="text-sm font-medium <?= $current === 'users'    ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-900' ?>">Users</a>
    </nav>
<?php }
