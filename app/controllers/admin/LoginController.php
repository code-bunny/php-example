<?php

require_once APP_ROOT . '/app/controllers/admin/AdminController.php';

class LoginController extends AdminController
{
    public function index(): string
    {
        $this->title = 'Sign in';
        $error = null;
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();

            $email    = trim($_POST['email']    ?? '');
            $password = trim($_POST['password'] ?? '');
            $users    = User::where('email', $email);
            $user     = $users[0] ?? null;

            if ($user && $user->authenticate($password)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user->id;
                $this->redirect('/admin');
            }

            http_response_code(422);
            $error = 'Invalid email or password.';
        }

        return $this->render('admin/login/index', compact('error', 'email'));
    }
}
