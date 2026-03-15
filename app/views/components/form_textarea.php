<?php

function form_textarea(string $name, string $label, string $value = '', int $rows = 6): void { ?>
    <div>
        <label for="<?= $name ?>" class="block text-sm font-medium text-gray-700 mb-1"><?= $label ?></label>
        <textarea id="<?= $name ?>" name="<?= $name ?>" rows="<?= $rows ?>"
                  class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($value) ?></textarea>
    </div>
<?php }
