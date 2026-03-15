<?php

require_once APP_ROOT . '/app/controllers/admin/AdminController.php';
require_once APP_ROOT . '/app/views/admin/_nav.php';
require_once APP_ROOT . '/app/views/components/alert.php';
require_once APP_ROOT . '/app/views/components/icons.php';

class AdminApiKeysController extends AdminController
{
    public function index(): string
    {
        $this->title = 'Admin — API Keys';
        $errors    = [];
        $new_token = $_SESSION['flash_api_token'] ?? null;
        unset($_SESSION['flash_api_token']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            csrf_verify();
            $name = trim($_POST['name'] ?? '');
            if ($name === '') $errors[] = 'Name is required.';

            if (empty($errors)) {
                $token = bin2hex(random_bytes(32));
                (new ApiKey(['name' => $name, 'token' => $token]))->save();
                flash('API key created.');
                $_SESSION['flash_api_token'] = $token;
                $_SESSION['flash_confetti']  = true;
                $this->redirect('/admin/api_keys');
            }

            http_response_code(422);
        }

        $keys = ApiKey::all();
        return $this->render('admin/api_keys/index', compact('keys', 'errors', 'new_token'));
    }
}
