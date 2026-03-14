<?php

require_once __DIR__ . '/../../../../db.php';
require_once __DIR__ . '/../../../../models/Post.php';
require_once __DIR__ . '/../../serialize.php';
Model::setDb($pdo);

header('Content-Type: application/vnd.api+json');

$post = Post::find($id);

if (!$post) {
    http_response_code(404);
    echo jsonapi_encode(['errors' => [['status' => '404', 'title' => 'Post not found.']]]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo jsonapi_encode([
        'data'  => serialize_post($post),
        'links' => ['self' => '/api/v1/posts/' . $post->id],
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $body  = json_decode(file_get_contents('php://input'), true);
    $attrs = $body['data']['attributes'] ?? [];

    if (isset($attrs['title'])) $post->title = $attrs['title'];
    if (isset($attrs['body']))  $post->body  = $attrs['body'];
    $post->save();

    echo jsonapi_encode([
        'data'  => serialize_post($post),
        'links' => ['self' => '/api/v1/posts/' . $post->id],
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $post->delete();
    http_response_code(204);
    exit;
}

http_response_code(405);
echo jsonapi_encode(['errors' => [['status' => '405', 'title' => 'Method not allowed.']]]);
