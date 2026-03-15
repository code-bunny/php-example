<?php

function form_input(string $name, string $label, string $value = ''): void { ?>
    <div>
        <label for="<?= $name ?>" class="block text-sm font-medium text-gray-700 mb-1"><?= $label ?></label>
        <input type="text" id="<?= $name ?>" name="<?= $name ?>"
               value="<?= htmlspecialchars($value) ?>"
               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
<?php }
