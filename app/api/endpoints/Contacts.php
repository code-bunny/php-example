<?php

require_once APP_ROOT . '/app/models/Contact.php';

resource('contacts', function () {

    get(function () {
        $page  = pageParams();
        $total = Contact::count();
        $last  = max(1, (int) ceil($total / $page['size']));

        return [
            'data'  => array_map(presentContact(...), Contact::paginate($page['size'], $page['offset'])),
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

        return [['data' => presentContact($contact), 'links' => ['self' => '/api/v1/contacts/' . $contact->id]], 201];
    });

    routeParam(':id', function () {

        get(function () {
            $contact = Contact::find(param('id'));
            if (!$contact) notFound('Contact not found.');

            return ['data' => presentContact($contact), 'links' => ['self' => '/api/v1/contacts/' . $contact->id]];
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

            return ['data' => presentContact($contact), 'links' => ['self' => '/api/v1/contacts/' . $contact->id]];
        });

        delete(function () {
            $contact = Contact::find(param('id'));
            if (!$contact) notFound('Contact not found.');

            $contact->delete();
            return null;
        });

    });

});

function presentContact(object $contact): array
{
    return [
        'type'       => 'contacts',
        'id'         => (string) $contact->id,
        'attributes' => [
            'email'     => $contact->email,
            'message'   => $contact->message,
            'createdAt' => $contact->created_at,
        ],
        'links' => ['self' => '/api/v1/contacts/' . $contact->id],
    ];
}
