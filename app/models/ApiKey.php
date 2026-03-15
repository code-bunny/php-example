<?php

require_once __DIR__ . '/Model.php';

class ApiKey extends Model {
    protected static string $table = 'api_keys';

    public static function findByToken(string $token): ?static {
        $results = static::where('token', $token);
        return $results[0] ?? null;
    }
}
