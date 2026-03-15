<?php

require_once APP_ROOT . '/app/controllers/api/ApiResource.php';
require_once __DIR__ . '/../../../models/Subscriber.php';

class SubscribersResource extends ApiResource {

    protected function present(object $subscriber): array {
        return [
            'type'       => 'subscribers',
            'id'         => (string) $subscriber->id,
            'attributes' => [
                'email'      => $subscriber->email,
                'created_at' => $subscriber->created_at,
            ],
            'links' => ['self' => '/api/v1/subscribers/' . $subscriber->id],
        ];
    }

    protected function index(): void {
        $page  = $this->pageParams();
        $total = Subscriber::count();
        $last  = max(1, (int) ceil($total / $page['size']));

        $this->respond([
            'data'  => array_map($this->present(...), Subscriber::paginate($page['size'], $page['offset'])),
            'links' => $this->paginationLinks('/api/v1/subscribers', $page['number'], $last, $page['size']),
            'meta'  => ['total' => $total],
        ]);
    }

    protected function show(string $id): void {
        $subscriber = Subscriber::find($id);
        if (!$subscriber) $this->notFound('Subscriber not found.');

        $this->respond([
            'data'  => $this->present($subscriber),
            'links' => ['self' => '/api/v1/subscribers/' . $subscriber->id],
        ]);
    }

    protected function create(): void {
        $attrs = $this->attributes();

        $errors = [];
        if (empty($attrs['email']) || !filter_var($attrs['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = ['status' => '422', 'title' => 'A valid email is required.'];
        }
        if ($errors) $this->unprocessable($errors);

        if (Subscriber::where('email', $attrs['email'])) {
            $this->conflict('Email already subscribed.');
        }

        $subscriber = new Subscriber(['email' => $attrs['email']]);
        $subscriber->save();

        $this->respond([
            'data'  => $this->present($subscriber),
            'links' => ['self' => '/api/v1/subscribers/' . $subscriber->id],
        ], 201);
    }

    protected function update(string $id): void {
        $subscriber = Subscriber::find($id);
        if (!$subscriber) $this->notFound('Subscriber not found.');

        $attrs  = $this->attributes();
        $errors = [];

        if (isset($attrs['email']) && !filter_var($attrs['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = ['status' => '422', 'title' => 'A valid email is required.'];
        }
        if ($errors) $this->unprocessable($errors);

        if (isset($attrs['email'])) $subscriber->email = $attrs['email'];
        $subscriber->save();

        $this->respond([
            'data'  => $this->present($subscriber),
            'links' => ['self' => '/api/v1/subscribers/' . $subscriber->id],
        ]);
    }

    protected function destroy(string $id): void {
        $subscriber = Subscriber::find($id);
        if (!$subscriber) $this->notFound('Subscriber not found.');

        $subscriber->delete();
        $this->noContent();
    }
}
