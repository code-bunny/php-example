<?php

// Router script for php -S
// Serves static files directly, except .yaml files which go through index.php for CORS headers.

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path !== '/' && is_file(__DIR__ . $path) && pathinfo($path, PATHINFO_EXTENSION) !== 'yaml') {
    return false;
}

require __DIR__ . '/index.php';
