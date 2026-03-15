<?php

require_once APP_ROOT . '/app/models/Post.php';
require_once APP_ROOT . '/app/api/serializers/PostSerializer.php';

resource('posts', function () {

    get(function () {
        $page  = pageParams();
        $total = Post::count();
        $last  = max(1, (int) ceil($total / $page['size']));

        return [
            'data'  => PostSerializer::many(Post::paginate($page['size'], $page['offset'])),
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

        return [['data' => PostSerializer::one($post)], 201];
    });

    get('latest', function () {
        return ['data' => PostSerializer::many(Post::paginate(10, 0))];
    });

    routeParam(':id', function () {

        get(function () {
            $post = Post::find(param('id'));
            if (!$post) notFound('Post not found.');

            return ['data' => PostSerializer::one($post)];
        });

        patch(function () {
            $post = Post::find(param('id'));
            if (!$post) notFound('Post not found.');

            $attrs = attributes();
            if (isset($attrs['title'])) $post->title = $attrs['title'];
            if (isset($attrs['body']))  $post->body  = $attrs['body'];
            $post->save();

            return ['data' => PostSerializer::one($post)];
        });

        delete(function () {
            $post = Post::find(param('id'));
            if (!$post) notFound('Post not found.');

            $post->delete();
            return null;
        });

    });

});
