<?php $app = require __DIR__ . '/config/app.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin') ?> — <?= htmlspecialchars($app['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module">
        import * as Turbo from 'https://esm.sh/@hotwired/turbo';
    </script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex flex-col">

    <header class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-5 py-4 flex items-center justify-between">
            <a href="/admin" class="text-sm font-semibold text-gray-900 tracking-widest uppercase"><?= htmlspecialchars($app['name']) ?> — Admin</a>
            <a href="/" class="text-sm text-gray-500 hover:text-gray-900">← Back to site</a>
        </div>
    </header>

    <main class="container mx-auto px-5 py-8 flex-grow">
        <?php $flash = flash_get(); ?>
        <?php if ($flash): ?>
            <?php
                $bg     = $flash['type'] === 'success' ? 'bg-green-50 border-green-300 text-green-800' : 'bg-red-50 border-red-300 text-red-800';
                $icon   = $flash['type'] === 'success' ? '✓' : '✕';
            ?>
            <div class="mb-6 flex items-center gap-2 border rounded-lg px-4 py-3 text-sm <?= $bg ?>">
                <span class="font-bold"><?= $icon ?></span>
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif ?>
        <?= $content ?>
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="container mx-auto px-5 py-4 text-center text-xs text-gray-400">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($app['name']) ?>
        </div>
    </footer>

    <?php if (!empty($_SESSION['flash_confetti'])): ?>
    <?php unset($_SESSION['flash_confetti']); ?>
    <script>
        function fireConfetti() {
            var colors = ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'];
            function side(origin, angle) {
                confetti({ particleCount: 80, spread: 60, angle: angle, origin: origin, colors: colors, startVelocity: 55, ticks: 200 });
            }
            side({ x: 0, y: 0.6 }, 60);
            side({ x: 1, y: 0.6 }, 120);
            confetti({ particleCount: 200, spread: 100, origin: { x: 0.5, y: 0.55 }, colors: colors, startVelocity: 45, ticks: 300, scalar: 1.1 });
            setTimeout(function () {
                side({ x: 0, y: 0.5 }, 60);
                side({ x: 1, y: 0.5 }, 120);
                confetti({ particleCount: 100, spread: 120, origin: { x: 0.5, y: 0.45 }, colors: colors, ticks: 250 });
            }, 400);
            setTimeout(function () {
                confetti({ particleCount: 150, spread: 160, origin: { x: 0.5, y: 0.3 }, colors: colors, ticks: 350, gravity: 0.6 });
            }, 750);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js" onload="fireConfetti()"></script>
    <?php endif ?>
</body>
</html>
