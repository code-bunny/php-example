<?php

// Returns the currently logged-in admin User, or null if not authenticated.
function admin_current_user(): ?User
{
    if (empty($_SESSION['user_id'])) return null;
    return User::find($_SESSION['user_id']);
}

// Redirect to the login page unless the session holds a valid admin user.
function admin_require_auth(): void
{
    if (!admin_current_user()) {
        header('Location: /admin/login');
        exit;
    }
}
