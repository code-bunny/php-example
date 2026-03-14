<?php

require_once __DIR__ . '/../../../db.php';
require_once __DIR__ . '/../../../models/ApiKey.php';
require_once __DIR__ . '/../_nav.php';
require_once __DIR__ . '/../../components/alert.php';
Model::setDb($pdo);

$title  = 'Admin — API Keys';
$errors = [];

// Retrieve newly created token from flash (shown once, then cleared)
$new_token = $_SESSION['flash_api_token'] ?? null;
unset($_SESSION['flash_api_token']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name = trim($_POST['name'] ?? '');
    if ($name === '') $errors[] = 'Name is required.';

    if (empty($errors)) {
        $token = bin2hex(random_bytes(32)); // 64-char secure random token
        (new ApiKey(['name' => $name, 'token' => $token]))->save();
        flash('API key created.');
        $_SESSION['flash_api_token'] = $token;
        $_SESSION['flash_confetti']  = true;
        header('Location: /admin/api_keys');
        exit;
    }

    http_response_code(422);
}

$keys = ApiKey::all();

?>

<?php admin_nav('api_keys') ?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">API Keys</h1>
</div>

<?php if ($new_token): ?>
    <div class="mb-6 bg-green-50 border border-green-300 rounded-lg p-4">
        <p class="text-sm font-medium text-green-800 mb-2">API key created — copy it now. You won't see it again.</p>
        <code class="block bg-white border border-green-200 rounded px-3 py-2 text-sm font-mono text-gray-800 break-all"><?= htmlspecialchars($new_token) ?></code>
    </div>
<?php endif ?>

<?php alert($errors) ?>

<div class="overflow-x-auto mb-8">
    <table class="w-full text-sm text-left border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Token</th>
                <th class="px-4 py-3">Created</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (empty($keys)): ?>
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">No API keys yet.</td></tr>
            <?php endif ?>
            <?php foreach ($keys as $key): ?>
                <tr class="bg-white">
                    <td class="px-4 py-3 font-medium text-gray-900"><?= htmlspecialchars($key->name) ?></td>
                    <td class="px-4 py-3 font-mono text-gray-400 text-xs">••••<?= htmlspecialchars(substr($key->token, -8)) ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($key->created_at) ?></td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="/admin/api_keys/<?= $key->id ?>/delete" class="inline"
                              onsubmit="return confirm('Revoke this API key?')">
                            <?php csrf_field() ?>
                            <button type="submit" class="text-red-600 hover:underline">Revoke</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<div class="bg-white border border-gray-200 rounded-lg p-6 max-w-md">
    <h2 class="text-base font-semibold mb-4">New API Key</h2>
    <form method="POST" class="flex gap-3 items-end">
        <?php csrf_field() ?>
        <div class="flex-1">
            <label for="name" class="block text-sm text-gray-600 mb-1">Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                   placeholder="e.g. Mobile app, CI pipeline"
                   class="w-full rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 text-sm outline-none text-gray-700 py-2 px-3">
        </div>
        <button type="submit" class="bg-indigo-500 text-white text-sm px-4 py-2 rounded hover:bg-indigo-600 whitespace-nowrap">
            Generate Key
        </button>
    </form>
</div>
