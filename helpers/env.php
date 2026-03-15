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

        // Shell environment takes precedence — don't overwrite vars already set
        // (e.g. APP_ENV=test passed when starting the test server).
        // Some PHP configs don't auto-populate $_ENV from the process environment,
        // so sync the value in so callers can use either $_ENV or getenv() consistently.
        if (getenv($key) !== false) {
            $_ENV[$key] ??= getenv($key);
            continue;
        }
        if (array_key_exists($key, $_ENV)) continue;

        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}
