<?php

require_once __DIR__ . '/../../../db.php';
require_once __DIR__ . '/../../../models/Contact.php';
require_once __DIR__ . '/../serialize.php';
Model::setDb($pdo);

header('Content-Type: application/vnd.api+json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $size   = max(1, (int) ($_GET['page']['size']   ?? 10));
    $number = max(1, (int) ($_GET['page']['number'] ?? 1));
    $offset = ($number - 1) * $size;
    $total  = Contact::count();
    $last   = max(1, (int) ceil($total / $size));

    $page_url = fn($n) => '/api/v1/contacts?page[number]=' . $n . '&page[size]=' . $size;

    echo jsonapi_encode([
        'data'  => array_map('serialize_contact', Contact::paginate($size, $offset)),
        'links' => [
            'self'  => $page_url($number),
            'first' => $page_url(1),
            'last'  => $page_url($last),
            'prev'  => $number > 1     ? $page_url($number - 1) : null,
            'next'  => $number < $last ? $page_url($number + 1) : null,
        ],
        'meta' => ['total' => $total],
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body  = json_decode(file_get_contents('php://input'), true);
    $attrs = $body['data']['attributes'] ?? [];

    $errors = [];
    if (empty($attrs['email']) || !filter_var($attrs['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = ['status' => '422', 'title' => 'A valid email is required.'];
    }
    if (empty($attrs['message'])) {
        $errors[] = ['status' => '422', 'title' => 'Message is required.'];
    }

    if ($errors) {
        http_response_code(422);
        echo jsonapi_encode(['errors' => $errors]);
        exit;
    }

    $contact = new Contact(['email' => $attrs['email'], 'message' => $attrs['message']]);
    $contact->save();

    http_response_code(201);
    echo jsonapi_encode([
        'data'  => serialize_contact($contact),
        'links' => ['self' => '/api/v1/contacts/' . $contact->id],
    ]);
    exit;
}

http_response_code(405);
echo jsonapi_encode(['errors' => [['status' => '405', 'title' => 'Method not allowed.']]]);
