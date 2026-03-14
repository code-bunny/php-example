<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin') ?> — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module">
        import * as Turbo from 'https://esm.sh/@hotwired/turbo';
    </script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">

    <header class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-5 py-4 flex items-center justify-between">
            <a href="/admin" class="text-sm font-semibold text-gray-900 tracking-widest uppercase">My App — Admin</a>
            <a href="/" class="text-sm text-gray-500 hover:text-gray-900">← Back to site</a>
        </div>
    </header>

    <main class="container mx-auto px-5 py-8 flex-grow">
        <?= $content ?>
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="container mx-auto px-5 py-4 text-center text-xs text-gray-400">
            &copy; <?= date('Y') ?> My App
        </div>
    </footer>

</body>
</html>
