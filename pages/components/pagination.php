<?php

function pagination(int $number, int $last, int $total, string $base_url): void {
    if ($last <= 1) return;

    $pages = array_unique(array_filter(array_merge(
        [1, 2],
        [$number - 1, $number, $number + 1],
        [$last - 1, $last]
    ), fn($p) => $p >= 1 && $p <= $last));
    sort($pages);

    $url = fn($p) => $base_url . '?page=' . $p;
    ?>
    <nav class="flex justify-center items-center gap-1 mt-8">

        <a href="<?= $url(max(1, $number - 1)) ?>"
           class="px-3 py-2 rounded text-sm <?= $number === 1 ? 'text-gray-300 pointer-events-none' : 'text-indigo-500 hover:bg-indigo-50' ?>">
            &laquo;
        </a>

        <?php $prev = null; foreach ($pages as $p): ?>
            <?php if ($prev !== null && $p - $prev > 1): ?>
                <span class="px-2 py-2 text-sm text-gray-400">&hellip;</span>
            <?php endif ?>
            <a href="<?= $url($p) ?>"
               class="px-3 py-2 rounded text-sm <?= $p === $number ? 'bg-indigo-500 text-white' : 'text-indigo-500 hover:bg-indigo-50' ?>">
                <?= $p ?>
            </a>
            <?php $prev = $p; endforeach ?>

        <a href="<?= $url(min($last, $number + 1)) ?>"
           class="px-3 py-2 rounded text-sm <?= $number === $last ? 'text-gray-300 pointer-events-none' : 'text-indigo-500 hover:bg-indigo-50' ?>">
            &raquo;
        </a>

    </nav>
    <p class="text-center text-sm text-gray-400 mt-2">Page <?= $number ?> of <?= $last ?> &mdash; <?= $total ?> total</p>
    <?php
}
