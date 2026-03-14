<?php

require_once __DIR__ . '/../../../../db.php';
require_once __DIR__ . '/../../../../models/Contact.php';
require_once __DIR__ . '/../../serialize.php';
Model::setDb($pdo);

header('Content-Type: application/vnd.api+json');

$contact = Contact::find($id);

if (!$contact) {
    http_response_code(404);
    echo jsonapi_encode(['errors' => [['status' => '404', 'title' => 'Contact not found.']]]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo jsonapi_encode([
        'data'  => serialize_contact($contact),
        'links' => ['self' => '/api/v1/contacts/' . $contact->id],
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $body  = json_decode(file_get_contents('php://input'), true);
    $attrs = $body['data']['attributes'] ?? [];

    $errors = [];
    if (isset($attrs['email']) && !filter_var($attrs['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = ['status' => '422', 'title' => 'A valid email is required.'];
    }

    if ($errors) {
        http_response_code(422);
        echo jsonapi_encode(['errors' => $errors]);
        exit;
    }

    if (isset($attrs['email']))   $contact->email   = $attrs['email'];
    if (isset($attrs['message'])) $contact->message = $attrs['message'];
    $contact->save();

    echo jsonapi_encode([
        'data'  => serialize_contact($contact),
        'links' => ['self' => '/api/v1/contacts/' . $contact->id],
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $contact->delete();
    http_response_code(204);
    exit;
}

http_response_code(405);
echo jsonapi_encode(['errors' => [['status' => '405', 'title' => 'Method not allowed.']]]);
