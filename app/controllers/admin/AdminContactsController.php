<?php

require_once APP_ROOT . '/app/controllers/admin/AdminController.php';
require_once APP_ROOT . '/app/views/admin/_nav.php';
require_once APP_ROOT . '/app/views/components/pagination.php';
require_once APP_ROOT . '/app/views/components/icons.php';

class AdminContactsController extends AdminController
{
    public function index(): string
    {
        $this->title = 'Admin — Contacts';
        $size     = 20;
        $number   = max(1, (int) ($_GET['page'] ?? 1));
        $offset   = ($number - 1) * $size;
        $total    = Contact::count();
        $last     = max(1, (int) ceil($total / $size));
        $contacts = Contact::paginate($size, $offset);
        return $this->render('admin/contacts/index', compact('contacts', 'number', 'last', 'total'));
    }

    public function show(string $id): string
    {
        $this->title = 'Contact';
        $contact = Contact::find($id);
        if (!$contact) {
            http_response_code(404);
            return '<p class="text-gray-500">Contact not found.</p>';
        }
        return $this->render('admin/contacts/show', ['contact' => $contact]);
    }
}
