<?php

require_once APP_ROOT . '/app/models/Subscriber.php';

resource('subscribers', function () {

    get(function () {
        $page  = pageParams();
        $total = Subscriber::count();
        $last  = max(1, (int) ceil($total / $page['size']));

        return [
            'data'  => array_map(presentSubscriber(...), Subscriber::paginate($page['size'], $page['offset'])),
            'links' => paginationLinks('/api/v1/subscribers', $page['number'], $last, $page['size']),
            'meta'  => ['total' => $total],
        ];
    });

    post(function () {
        $attrs  = attributes();
        $errors = [];
        if (empty($attrs['email']) || !filter_var($attrs['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = ['status' => '422', 'title' => 'A valid email is required.'];
        }
        if ($errors) unprocessable($errors);

        if (Subscriber::where('email', $attrs['email'])) {
            conflict('Email already subscribed.');
        }

        $subscriber = new Subscriber(['email' => $attrs['email']]);
        $subscriber->save();

        return [['data' => presentSubscriber($subscriber), 'links' => ['self' => '/api/v1/subscribers/' . $subscriber->id]], 201];
    });

    routeParam(':id', function () {

        get(function () {
            $subscriber = Subscriber::find(param('id'));
            if (!$subscriber) notFound('Subscriber not found.');

            return ['data' => presentSubscriber($subscriber), 'links' => ['self' => '/api/v1/subscribers/' . $subscriber->id]];
        });

        patch(function () {
            $subscriber = Subscriber::find(param('id'));
            if (!$subscriber) notFound('Subscriber not found.');

            $attrs  = attributes();
            $errors = [];
            if (isset($attrs['email']) && !filter_var($attrs['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = ['status' => '422', 'title' => 'A valid email is required.'];
            }
            if ($errors) unprocessable($errors);

            if (isset($attrs['email'])) $subscriber->email = $attrs['email'];
            $subscriber->save();

            return ['data' => presentSubscriber($subscriber), 'links' => ['self' => '/api/v1/subscribers/' . $subscriber->id]];
        });

        delete(function () {
            $subscriber = Subscriber::find(param('id'));
            if (!$subscriber) notFound('Subscriber not found.');

            $subscriber->delete();
            return null;
        });

    });

});

function presentSubscriber(object $subscriber): array
{
    return [
        'type'       => 'subscribers',
        'id'         => (string) $subscriber->id,
        'attributes' => [
            'email'     => $subscriber->email,
            'createdAt' => $subscriber->created_at,
        ],
        'links' => ['self' => '/api/v1/subscribers/' . $subscriber->id],
    ];
}
