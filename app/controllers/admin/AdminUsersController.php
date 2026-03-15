<?php

require_once APP_ROOT . '/app/controllers/admin/AdminController.php';
require_once APP_ROOT . '/app/views/admin/_nav.php';
require_once APP_ROOT . '/app/views/components/icons.php';

class AdminUsersController extends AdminController
{
    public function index(): string
    {
        $this->title = 'Admin — Users';
        $users = User::all();
        return $this->render('admin/users/index', ['users' => $users]);
    }

    public function create(): string
    {
        $this->title = 'New User';
        return $this->form(null);
    }

    public function edit(string $id): string
    {
        $user = User::find($id);
        if (!$user) {
            http_response_code(404);
            $this->title = '404';
            return '<p class="text-gray-500">User not found.</p>';
        }
        $this->title = 'Edit User';
        return $this->form($user);
    }

    private function form(?User $user): string
    {
        require_once APP_ROOT . '/app/views/components/form_input.php';
        require_once APP_ROOT . '/app/views/components/alert.php';

        $errors = [];
        $email  = $_POST['email'] ?? $user?->email ?? '';
        $role   = $_POST['role']  ?? $user?->role  ?? 'admin';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();

            $email    = trim($_POST['email']    ?? '');
            $password = trim($_POST['password'] ?? '');
            $role     = trim($_POST['role']     ?? 'admin');

            if ($email === '')                                          $errors[] = 'Email is required.';
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL))  $errors[] = 'Email is invalid.';
            if (!$user && $password === '')                            $errors[] = 'Password is required.';
            if ($password !== '' && strlen($password) < 8)            $errors[] = 'Password must be at least 8 characters.';

            if ($email && empty($errors)) {
                $existing = User::where('email', $email);
                if (!empty($existing) && $existing[0]->id !== ($user?->id)) {
                    $errors[] = 'Email is already taken.';
                }
            }

            if (empty($errors)) {
                $is_new = ($user === null);
                if ($user) {
                    $user->email = $email;
                    $user->role  = $role;
                    if ($password !== '') $user->setPassword($password);
                } else {
                    $user = new User(['email' => $email, 'role' => $role]);
                    $user->setPassword($password);
                }
                $user->save();
                flash($is_new ? 'User created.' : 'User updated.');
                $this->redirect('/admin/users');
            }

            http_response_code(422);
        }

        return $this->render('admin/users/edit', compact('user', 'email', 'role', 'errors'));
    }
}
