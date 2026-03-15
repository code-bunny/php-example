<?php

require_once APP_ROOT . '/app/api/Endpoint.php';
require_once APP_ROOT . '/app/api/endpoints/Posts.php';
require_once APP_ROOT . '/app/api/endpoints/Contacts.php';
require_once APP_ROOT . '/app/api/endpoints/Subscribers.php';

class Api
{
    private static string $prefix  = '';
    private static array  $routes  = [];

    public static function prefix(string $prefix): void
    {
        self::$prefix = rtrim($prefix, '/');
    }

    public static function mount(string $endpointClass): void
    {
        $endpoint = new $endpointClass();
        $endpoint->register();

        foreach ($endpoint->getRoutes() as $route) {
            self::$routes[] = [
                'method'   => $route['method'],
                'pattern'  => self::$prefix . $route['pattern'],
                'handler'  => $route['handler'],
                'endpoint' => $endpoint,
            ];
        }
    }

    public static function dispatch(string $method, string $path): bool
    {
        foreach (self::$routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            $params = self::match($route['pattern'], $path);
            if ($params === null) {
                continue;
            }

            $route['endpoint']->setUrlParams($params);
            ($route['handler'])();
            return true;
        }

        return false;
    }

    public static function notFound(): never
    {
        header('Content-Type: application/vnd.api+json');
        http_response_code(404);
        echo json_encode([
            'jsonapi' => ['version' => '1.1'],
            'errors'  => [['status' => '404', 'title' => 'Not found.']],
        ], JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Convert pattern like /posts/:id to a regex, return named captures or false
    private static function match(string $pattern, string $path): ?array
    {
        $regex = preg_replace('/:([a-z_]+)/', '(?P<$1>[0-9a-f-]{36})', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $m)) {
            return null;
        }

        // Return only named captures
        return array_filter($m, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
    }
}
