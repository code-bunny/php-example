<?php

require_once APP_ROOT . '/app/models/Post.php';

resource('posts', function () {

    get(function () {
        $page  = pageParams();
        $total = Post::count();
        $last  = max(1, (int) ceil($total / $page['size']));

        return [
            'data'  => array_map(presentPost(...), Post::paginate($page['size'], $page['offset'])),
            'links' => paginationLinks('/api/v1/posts', $page['number'], $last, $page['size']),
            'meta'  => ['total' => $total],
        ];
    });

    post(function () {
        $attrs  = attributes();
        $errors = [];
        if (empty($attrs['title'])) $errors[] = ['status' => '422', 'title' => 'Title is required.'];
        if (empty($attrs['body']))  $errors[] = ['status' => '422', 'title' => 'Body is required.'];
        if ($errors) unprocessable($errors);

        $post = new Post(['title' => $attrs['title'], 'body' => $attrs['body']]);
        $post->save();

        return [['data' => presentPost($post), 'links' => ['self' => '/api/v1/posts/' . $post->id]], 201];
    });

    routeParam(':id', function () {

        get(function () {
            $post = Post::find(param('id'));
            if (!$post) notFound('Post not found.');

            return ['data' => presentPost($post), 'links' => ['self' => '/api/v1/posts/' . $post->id]];
        });

        patch(function () {
            $post = Post::find(param('id'));
            if (!$post) notFound('Post not found.');

            $attrs = attributes();
            if (isset($attrs['title'])) $post->title = $attrs['title'];
            if (isset($attrs['body']))  $post->body  = $attrs['body'];
            $post->save();

            return ['data' => presentPost($post), 'links' => ['self' => '/api/v1/posts/' . $post->id]];
        });

        delete(function () {
            $post = Post::find(param('id'));
            if (!$post) notFound('Post not found.');

            $post->delete();
            return null;
        });

    });

});

function presentPost(object $post): array
{
    return [
        'type'       => 'posts',
        'id'         => (string) $post->id,
        'attributes' => [
            'title'     => $post->title,
            'body'      => $post->body,
            'createdAt' => $post->created_at,
            'updatedAt' => $post->updated_at,
        ],
        'links' => ['self' => '/api/v1/posts/' . $post->id],
    ];
}
