<?php

require_once APP_ROOT . '/app/models/Contact.php';

class Contacts extends Endpoint
{
    public function register(): void
    {
        $this->resource('contacts', function () {

            $this->get(function () {
                $page  = $this->pageParams();
                $total = Contact::count();
                $last  = max(1, (int) ceil($total / $page['size']));

                $this->respond([
                    'data'  => array_map($this->present(...), Contact::paginate($page['size'], $page['offset'])),
                    'links' => $this->paginationLinks('/api/v1/contacts', $page['number'], $last, $page['size']),
                    'meta'  => ['total' => $total],
                ]);
            });

            $this->post(function () {
                $attrs  = $this->attributes();
                $errors = [];
                if (empty($attrs['email']) || !filter_var($attrs['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = ['status' => '422', 'title' => 'A valid email is required.'];
                }
                if (empty($attrs['message'])) {
                    $errors[] = ['status' => '422', 'title' => 'Message is required.'];
                }
                if ($errors) $this->unprocessable($errors);

                $contact = new Contact(['email' => $attrs['email'], 'message' => $attrs['message']]);
                $contact->save();

                $this->respond([
                    'data'  => $this->present($contact),
                    'links' => ['self' => '/api/v1/contacts/' . $contact->id],
                ], 201);
            });

            $this->routeParam(':id', function () {

                $this->get(function () {
                    $contact = Contact::find($this->param('id'));
                    if (!$contact) $this->notFound('Contact not found.');

                    $this->respond([
                        'data'  => $this->present($contact),
                        'links' => ['self' => '/api/v1/contacts/' . $contact->id],
                    ]);
                });

                $this->patch(function () {
                    $contact = Contact::find($this->param('id'));
                    if (!$contact) $this->notFound('Contact not found.');

                    $attrs  = $this->attributes();
                    $errors = [];
                    if (isset($attrs['email']) && !filter_var($attrs['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = ['status' => '422', 'title' => 'A valid email is required.'];
                    }
                    if ($errors) $this->unprocessable($errors);

                    if (isset($attrs['email']))   $contact->email   = $attrs['email'];
                    if (isset($attrs['message'])) $contact->message = $attrs['message'];
                    $contact->save();

                    $this->respond([
                        'data'  => $this->present($contact),
                        'links' => ['self' => '/api/v1/contacts/' . $contact->id],
                    ]);
                });

                $this->delete(function () {
                    $contact = Contact::find($this->param('id'));
                    if (!$contact) $this->notFound('Contact not found.');

                    $contact->delete();
                    $this->noContent();
                });
            });
        });
    }

    private function present(object $contact): array
    {
        return [
            'type'       => 'contacts',
            'id'         => (string) $contact->id,
            'attributes' => [
                'email'      => $contact->email,
                'message'    => $contact->message,
                'createdAt' => $contact->created_at,
            ],
            'links' => ['self' => '/api/v1/contacts/' . $contact->id],
        ];
    }
}
