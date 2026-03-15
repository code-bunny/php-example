<?php

require_once __DIR__ . '/Model.php';

class User extends Model
{
    protected static string $table = 'users';

    // PHP's equivalent of Rails has_secure_password.
    // Call setPassword() with a plain-text password before save(); it hashes it
    // with bcrypt and stores the digest. Never set password_digest directly.
    //
    // Cost 12 in production (stronger than PHP's default of 10).
    // Cost 4 in test — the minimum valid bcrypt cost, essentially instant,
    // so bcrypt doesn't dominate test run time.
    public function setPassword(string $password): void
    {
        $env  = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'development';
        $cost = $env === 'test' ? 4 : 12;
        $this->attributes['password_digest'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
    }

    // Returns true if the given plain-text password matches the stored digest.
    public function authenticate(string $password): bool
    {
        return password_verify($password, $this->attributes['password_digest'] ?? '');
    }
}
