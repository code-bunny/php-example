<?php

require_once APP_ROOT . '/app/controllers/Controller.php';

class ContactController extends Controller
{
    public function index(): string
    {
        $this->title = 'Contact';
        $errors  = [];
        $success = false;
        $email   = '';
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            rate_limit('contact', max: 5, window: 30 * 60);

            $email   = trim($_POST['email']   ?? '');
            $message = trim($_POST['message'] ?? '');

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email address is required.';
            }
            if ($message === '') {
                $errors[] = 'Message is required.';
            }

            if (!empty($errors)) {
                http_response_code(422);
            } else {
                (new Contact(['email' => $email, 'message' => $message]))->save();
                $success = true;
                $email   = '';
                $message = '';
            }
        }

        return $this->render('contact/index', compact('errors', 'success', 'email', 'message'));
    }
}
