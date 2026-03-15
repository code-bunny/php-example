<?php

require_once APP_ROOT . '/app/models/Subscriber.php';

class Subscribers extends Endpoint
{
    public function register(): void
    {
        $this->resource('subscribers', function () {

            $this->get(function () {
                $page  = $this->pageParams();
                $total = Subscriber::count();
                $last  = max(1, (int) ceil($total / $page['size']));

                $this->respond([
                    'data'  => array_map($this->present(...), Subscriber::paginate($page['size'], $page['offset'])),
                    'links' => $this->paginationLinks('/api/v1/subscribers', $page['number'], $last, $page['size']),
                    'meta'  => ['total' => $total],
                ]);
            });

            $this->post(function () {
                $attrs  = $this->attributes();
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
            });

            $this->routeParam(':id', function () {

                $this->get(function () {
                    $subscriber = Subscriber::find($this->param('id'));
                    if (!$subscriber) $this->notFound('Subscriber not found.');

                    $this->respond([
                        'data'  => $this->present($subscriber),
                        'links' => ['self' => '/api/v1/subscribers/' . $subscriber->id],
                    ]);
                });

                $this->patch(function () {
                    $subscriber = Subscriber::find($this->param('id'));
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
                });

                $this->delete(function () {
                    $subscriber = Subscriber::find($this->param('id'));
                    if (!$subscriber) $this->notFound('Subscriber not found.');

                    $subscriber->delete();
                    $this->noContent();
                });
            });
        });
    }

    private function present(object $subscriber): array
    {
        return [
            'type'       => 'subscribers',
            'id'         => (string) $subscriber->id,
            'attributes' => [
                'email'      => $subscriber->email,
                'createdAt' => $subscriber->created_at,
            ],
            'links' => ['self' => '/api/v1/subscribers/' . $subscriber->id],
        ];
    }
}
