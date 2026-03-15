<?php

require_once APP_ROOT . '/app/models/Contact.php';
require_once APP_ROOT . '/app/api/serializers/ContactSerializer.php';

resource('contacts', function () {

    get(function () {
        $page  = pageParams();
        $total = Contact::count();
        $last  = max(1, (int) ceil($total / $page['size']));

        return [
            'data'  => ContactSerializer::many(Contact::paginate($page['size'], $page['offset'])),
            'links' => paginationLinks('/api/v1/contacts', $page['number'], $last, $page['size']),
            'meta'  => ['total' => $total],
        ];
    });

    post(function () {
        $attrs  = attributes();
        $errors = [];
        if (empty($attrs['email']) || !filter_var($attrs['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = ['status' => '422', 'title' => 'A valid email is required.'];
        }
        if (empty($attrs['message'])) {
            $errors[] = ['status' => '422', 'title' => 'Message is required.'];
        }
        if ($errors) unprocessable($errors);

        $contact = new Contact(['email' => $attrs['email'], 'message' => $attrs['message']]);
        $contact->save();

        return [['data' => ContactSerializer::one($contact)], 201];
    });

    routeParam(':id', function () {

        get(function () {
            $contact = Contact::find(param('id'));
            if (!$contact) notFound('Contact not found.');

            return ['data' => ContactSerializer::one($contact)];
        });

        patch(function () {
            $contact = Contact::find(param('id'));
            if (!$contact) notFound('Contact not found.');

            $attrs  = attributes();
            $errors = [];
            if (isset($attrs['email']) && !filter_var($attrs['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = ['status' => '422', 'title' => 'A valid email is required.'];
            }
            if ($errors) unprocessable($errors);

            if (isset($attrs['email']))   $contact->email   = $attrs['email'];
            if (isset($attrs['message'])) $contact->message = $attrs['message'];
            $contact->save();

            return ['data' => ContactSerializer::one($contact)];
        });

        delete(function () {
            $contact = Contact::find(param('id'));
            if (!$contact) notFound('Contact not found.');

            $contact->delete();
            return null;
        });

    });

});
