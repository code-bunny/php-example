<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../models/Subscriber.php';
Model::setDb($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

csrf_verify();
rate_limit('subscribe', max: 3, window: 30 * 60); // 3 attempts per 30 minutes

$email  = trim($_POST['email'] ?? '');
$result = '';   // 'subscribed' | 'already_subscribed'

if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $existing = Subscriber::where('email', $email);
    if (empty($existing)) {
        (new Subscriber(['email' => $email]))->save();
        $result = 'subscribed';
    } else {
        $result = 'already_subscribed';
    }
}

// Only redirect back to the same origin — never follow an external URL
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$parsed  = parse_url($referer);
$safe    = (isset($parsed['host']) && $parsed['host'] === $_SERVER['HTTP_HOST'])
    ? ($parsed['path'] ?? '/')
    : '/';

header('Location: ' . $safe . ($result ? '?' . $result . '=1' : ''));
exit;
