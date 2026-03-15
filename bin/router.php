<?php

// Router script for php -S
// Serves static files directly, except .yaml files which go through index.php for CORS headers.

$root = dirname(__DIR__);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path !== '/' && is_file($root . $path) && pathinfo($path, PATHINFO_EXTENSION) !== 'yaml') {
    return false;
}

require $root . '/index.php';
