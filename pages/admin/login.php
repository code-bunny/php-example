<?php

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $users    = User::where('email', $email);
    $user     = $users[0] ?? null;

    if ($user && $user->authenticate($password)) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
        header('Location: /admin');
        exit;
    }

    http_response_code(422);
    $error = 'Invalid email or password.';
}

$title = 'Sign in';

ob_start();
?>
<div class="max-w-sm mx-auto mt-16">
    <h1 class="text-2xl font-bold mb-8 text-center">Admin sign in</h1>

    <?php if ($error): ?>
        <div class="mb-4 border rounded-lg px-4 py-3 text-sm bg-red-50 border-red-300 text-red-800">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif ?>

    <form method="POST" action="/admin/login" class="space-y-4 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <?php csrf_field() ?>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" required autofocus
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 rounded-lg transition">
            Sign in
        </button>
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../admin_layout.php';
