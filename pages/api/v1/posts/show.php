<?php

require_once __DIR__ . '/../../../../db.php';
require_once __DIR__ . '/../../../../models/Post.php';
require_once __DIR__ . '/../../serialize.php';
Model::setDb($pdo);

header('Content-Type: application/vnd.api+json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo jsonapi_encode(['jsonapi' => (object) [], 'errors' => [['status' => '405', 'title' => 'Method not allowed.']]]);
    exit;
}

$post = Post::find((int) $id);

if (!$post) {
    http_response_code(404);
    echo jsonapi_encode(['jsonapi' => (object) [], 'errors' => [['status' => '404', 'title' => 'Post not found.']]]);
    exit;
}

echo jsonapi_encode([
    'jsonapi' => (object) [],
    'data' => serialize_post($post),
    'links' => ['self' => '/api/v1/posts/' . $post->id],
]);
