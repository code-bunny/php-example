<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../models/Subscriber.php';
Model::setDb($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    rate_limit('subscribe', max: 3, window: 30 * 60); // 3 attempts per 30 minutes

    $email = trim($_POST['email'] ?? '');

    if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $existing = Subscriber::where('email', $email);
        if (empty($existing)) {
            $subscriber = new Subscriber(['email' => $email]);
            $subscriber->save();
        }
    }
}

$redirect = $_SERVER['HTTP_REFERER'] ?? '/';
header('Location: ' . $redirect . '?subscribed=1');
exit;
