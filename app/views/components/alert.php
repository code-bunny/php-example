<?php

function alert(array $messages): void {
    if (empty($messages)) return; ?>
    <div class="mb-4 bg-red-50 border border-red-200 rounded p-3 text-sm text-red-700 space-y-1">
        <?php foreach ($messages as $message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endforeach; ?>
    </div>
<?php }
