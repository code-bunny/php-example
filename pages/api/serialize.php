<?php

function jsonapi_encode(mixed $data): string {
    return json_encode($data, JSON_UNESCAPED_SLASHES);
}

function serialize_post(Post $post): array {
    return [
        'type' => 'posts',
        'id'   => (string) $post->id,
        'attributes' => [
            'title'      => $post->title,
            'body'       => $post->body,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
        ],
        'links' => ['self' => '/api/posts/' . $post->id],
    ];
}
