<?php

require_once APP_ROOT . '/app/api/ApiResource.php';
require_once APP_ROOT . '/app/models/Post.php';

class PostsResource extends ApiResource {

    protected function present(object $post): array {
        return [
            'type'       => 'posts',
            'id'         => (string) $post->id,
            'attributes' => [
                'title'      => $post->title,
                'body'       => $post->body,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ],
            'links' => ['self' => '/api/v1/posts/' . $post->id],
        ];
    }

    protected function index(): void {
        $page  = $this->pageParams();
        $total = Post::count();
        $last  = max(1, (int) ceil($total / $page['size']));

        $this->respond([
            'data'  => array_map($this->present(...), Post::paginate($page['size'], $page['offset'])),
            'links' => $this->paginationLinks('/api/v1/posts', $page['number'], $last, $page['size']),
            'meta'  => ['total' => $total],
        ]);
    }

    protected function show(string $id): void {
        $post = Post::find($id);
        if (!$post) $this->notFound('Post not found.');

        $this->respond([
            'data'  => $this->present($post),
            'links' => ['self' => '/api/v1/posts/' . $post->id],
        ]);
    }

    protected function create(): void {
        $attrs = $this->attributes();

        $errors = [];
        if (empty($attrs['title'])) $errors[] = ['status' => '422', 'title' => 'Title is required.'];
        if (empty($attrs['body']))  $errors[] = ['status' => '422', 'title' => 'Body is required.'];
        if ($errors) $this->unprocessable($errors);

        $post = new Post(['title' => $attrs['title'], 'body' => $attrs['body']]);
        $post->save();

        $this->respond([
            'data'  => $this->present($post),
            'links' => ['self' => '/api/v1/posts/' . $post->id],
        ], 201);
    }

    protected function update(string $id): void {
        $post = Post::find($id);
        if (!$post) $this->notFound('Post not found.');

        $attrs = $this->attributes();
        if (isset($attrs['title'])) $post->title = $attrs['title'];
        if (isset($attrs['body']))  $post->body  = $attrs['body'];
        $post->save();

        $this->respond([
            'data'  => $this->present($post),
            'links' => ['self' => '/api/v1/posts/' . $post->id],
        ]);
    }

    protected function destroy(string $id): void {
        $post = Post::find($id);
        if (!$post) $this->notFound('Post not found.');

        $post->delete();
        $this->noContent();
    }
}
