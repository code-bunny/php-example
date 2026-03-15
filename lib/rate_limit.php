<?php

function rate_limit(string $key, int $max = 10, int $window = 60): void {
    $ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $file = sys_get_temp_dir() . '/rl_' . md5($ip . $key) . '.json';

    $data = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : ['count' => 0, 'reset_at' => time() + $window];

    if (time() > $data['reset_at']) {
        $data = ['count' => 0, 'reset_at' => time() + $window];
    }

    $data['count']++;
    file_put_contents($file, json_encode($data), LOCK_EX);

    if ($data['count'] > $max) {
        http_response_code(429);
        exit('Too many requests. Please try again later.');
    }
}
