<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'My App') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">

    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center gap-6">
            <a href="/" class="font-semibold text-gray-900 hover:text-blue-600">My App</a>
            <a href="/about" class="text-sm text-gray-600 hover:text-blue-600">About</a>
            <a href="/contact" class="text-sm text-gray-600 hover:text-blue-600">Contact</a>
            <a href="/posts/new" class="ml-auto text-sm bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700">New Post</a>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-8">
        <?= $content ?>
    </main>

</body>
</html>
