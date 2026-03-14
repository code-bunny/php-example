<?php

require_once __DIR__ . '/../../../db.php';
require_once __DIR__ . '/../../../models/Subscriber.php';
require_once __DIR__ . '/../serialize.php';
Model::setDb($pdo);

header('Content-Type: application/vnd.api+json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo jsonapi_encode(['errors' => [['status' => '405', 'title' => 'Method not allowed.']]]);
    exit;
}

$size   = max(1, (int) ($_GET['page']['size']   ?? 10));
$number = max(1, (int) ($_GET['page']['number'] ?? 1));
$offset = ($number - 1) * $size;
$total  = Subscriber::count();
$last   = max(1, (int) ceil($total / $size));

$page_url = fn($n) => '/api/v1/subscribers?page[number]=' . $n . '&page[size]=' . $size;

echo jsonapi_encode([
    'data'  => array_map('serialize_subscriber', Subscriber::paginate($size, $offset)),
    'links' => [
        'self'  => $page_url($number),
        'first' => $page_url(1),
        'last'  => $page_url($last),
        'prev'  => $number > 1     ? $page_url($number - 1) : null,
        'next'  => $number < $last ? $page_url($number + 1) : null,
    ],
    'meta' => ['total' => $total],
]);
