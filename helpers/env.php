<?php

// Loads a .env file into $_ENV and putenv().
// Lines starting with # are treated as comments.
// Inline quotes are stripped: KEY="value" → value

function load_env(string $file): void {
    if (!file_exists($file)) return;

    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // Strip surrounding quotes
        if (preg_match('/^(["\'])(.*)(\1)$/', $value, $m)) {
            $value = $m[2];
        }

        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}
