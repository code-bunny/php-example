<?php

require_once APP_ROOT . '/app/models/Subscriber.php';
require_once APP_ROOT . '/app/api/serializers/SubscriberSerializer.php';

resource('subscribers', function () {

    get(function () {
        $page  = pageParams();
        $total = Subscriber::count();
        $last  = max(1, (int) ceil($total / $page['size']));

        return [
            'data'  => SubscriberSerializer::many(Subscriber::paginate($page['size'], $page['offset'])),
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

        return [['data' => SubscriberSerializer::one($subscriber)], 201];
    });

    routeParam(':id', function () {

        get(function () {
            $subscriber = Subscriber::find(param('id'));
            if (!$subscriber) notFound('Subscriber not found.');

            return ['data' => SubscriberSerializer::one($subscriber)];
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

            return ['data' => SubscriberSerializer::one($subscriber)];
        });

        delete(function () {
            $subscriber = Subscriber::find(param('id'));
            if (!$subscriber) notFound('Subscriber not found.');

            $subscriber->delete();
            return null;
        });

    });

});
