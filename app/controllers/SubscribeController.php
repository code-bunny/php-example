<?php

require_once APP_ROOT . '/app/controllers/Controller.php';

class SubscribeController extends Controller
{
    public function store(): never
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }

        csrf_verify();
        rate_limit('subscribe', max: 3, window: 30 * 60);

        $email  = trim($_POST['email'] ?? '');
        $result = '';

        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $existing = Subscriber::where('email', $email);
            if (empty($existing)) {
                (new Subscriber(['email' => $email]))->save();
                $result = 'subscribed';
            } else {
                $result = 'already_subscribed';
            }
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $parsed  = parse_url($referer);
        $safe    = (isset($parsed['host']) && $parsed['host'] === $_SERVER['HTTP_HOST'])
            ? ($parsed['path'] ?? '/')
            : '/';

        $this->redirect($safe . ($result ? '?' . $result . '=1' : ''));
    }
}
