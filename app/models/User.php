<?php

require_once __DIR__ . '/Model.php';

class User extends Model
{
    protected static string $table = 'users';

    // PHP's equivalent of Rails has_secure_password.
    // Call setPassword() with a plain-text password before save(); it hashes it
    // with bcrypt and stores the digest. Never set password_digest directly.
    public function setPassword(string $password): void
    {
        $this->attributes['password_digest'] = password_hash($password, PASSWORD_DEFAULT);
    }

    // Returns true if the given plain-text password matches the stored digest.
    public function authenticate(string $password): bool
    {
        return password_verify($password, $this->attributes['password_digest'] ?? '');
    }
}
