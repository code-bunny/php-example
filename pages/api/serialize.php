<?php

function jsonapi_encode(mixed $data): string {
    return json_encode($data, JSON_UNESCAPED_SLASHES);
}

function serialize_contact(Contact $contact): array {
    return [
        'type' => 'contacts',
        'id'   => (string) $contact->id,
        'attributes' => [
            'email'      => $contact->email,
            'message'    => $contact->message,
            'created_at' => $contact->created_at,
        ],
        'links' => ['self' => '/api/v1/contacts/' . $contact->id],
    ];
}

function serialize_subscriber(Subscriber $subscriber): array {
    return [
        'type' => 'subscribers',
        'id'   => (string) $subscriber->id,
        'attributes' => [
            'email'      => $subscriber->email,
            'created_at' => $subscriber->created_at,
        ],
        'links' => ['self' => '/api/v1/subscribers/' . $subscriber->id],
    ];
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
        'links' => ['self' => '/api/v1/posts/' . $post->id],
    ];
}
