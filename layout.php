<?php $app = require __DIR__ . '/config/app.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? $app['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module">
        import * as Turbo from 'https://esm.sh/@hotwired/turbo';
    </script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <?php require __DIR__ . '/pages/shared/_header.php' ?>

    <?php if (isset($_GET['subscribed'])): ?>
    <div class="bg-indigo-500 text-white text-sm text-center py-2 px-4">
        You're subscribed — thanks!
    </div>
    <?php elseif (isset($_GET['already_subscribed'])): ?>
    <div class="bg-amber-400 text-amber-900 text-sm text-center py-2 px-4">
        You're already subscribed with that address.
    </div>
    <?php endif; ?>

    <main class="container mx-auto px-5 py-8 flex-grow">
        <?= $content ?>
    </main>

    <?php require __DIR__ . '/pages/shared/_footer.php' ?>

</body>
</html>
