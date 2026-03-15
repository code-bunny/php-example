<?php

class Route
{
    private static string $prefix    = '';
    private static array  $routes    = [];
    private static array  $stack     = [];
    private static array  $urlParams = [];

    // ── DSL stack (used by resource() and routeParam() global functions) ──────

    public static function push(string $segment): void { self::$stack[] = $segment; }
    public static function pop(): void                 { array_pop(self::$stack); }

    public static function addRoute(string $method, callable $fn): void
    {
        self::$routes[] = [
            'method'  => $method,
            'pattern' => self::$prefix . (implode('', self::$stack) ?: '/'),
            'handler' => $fn,
        ];
    }

    // ── Prefix (set before endpoint files are required) ───────────────────────

    public static function prefix(string $prefix): void
    {
        self::$prefix = rtrim($prefix, '/');
    }

    // ── Dispatch ──────────────────────────────────────────────────────────────

    public static function dispatch(string $method, string $path): bool
    {
        foreach (self::$routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            $params = self::matchPattern($route['pattern'], $path);
            if ($params === null) {
                continue;
            }

            self::$urlParams = $params;
            $result = ($route['handler'])();

            if ($result === null) {
                http_response_code(204);
            } elseif (isset($result[1]) && is_int($result[1])) {
                self::sendJson($result[0], $result[1]);
            } else {
                self::sendJson($result, 200);
            }

            return true;
        }

        return false;
    }

    // ── URL params ────────────────────────────────────────────────────────────

    public static function param(string $name): ?string
    {
        return self::$urlParams[$name] ?? null;
    }

    // ── Request helpers ───────────────────────────────────────────────────────

    public static function attributes(): array
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        return $body['data']['attributes'] ?? [];
    }

    public static function pageParams(): array
    {
        $size   = min(100, max(1, (int) ($_GET['page']['size']   ?? 10)));
        $number = max(1,             (int) ($_GET['page']['number'] ?? 1));
        return ['size' => $size, 'number' => $number, 'offset' => ($number - 1) * $size];
    }

    public static function paginationLinks(string $base, int $number, int $last, int $size): array
    {
        $url = fn($n) => "$base?page[number]=$n&page[size]=$size";
        return [
            'self'  => $url($number),
            'first' => $url(1),
            'last'  => $url($last),
            'prev'  => $number > 1     ? $url($number - 1) : null,
            'next'  => $number < $last ? $url($number + 1) : null,
        ];
    }

    // ── Error responses ───────────────────────────────────────────────────────

    public static function notFound(string $message = 'Not found.'): never
    {
        self::sendJson(['errors' => [['status' => '404', 'title' => $message]]], 404);
        exit;
    }

    public static function unprocessable(array $errors): never
    {
        self::sendJson(['errors' => $errors], 422);
        exit;
    }

    public static function conflict(string $message): never
    {
        self::sendJson(['errors' => [['status' => '409', 'title' => $message]]], 409);
        exit;
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    private static function sendJson(array $data, int $status): void
    {
        header('Content-Type: application/vnd.api+json');
        http_response_code($status);
        echo json_encode(['jsonapi' => ['version' => '1.1']] + $data, JSON_UNESCAPED_SLASHES);
    }

    private static function matchPattern(string $pattern, string $path): ?array
    {
        $regex = preg_replace('/:([a-z_]+)/', '(?P<$1>[0-9a-f-]{36})', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $m)) {
            return null;
        }

        return array_filter($m, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
    }
}

// ── DSL global functions ──────────────────────────────────────────────────────

function resource(string $name, callable $fn): void
{
    Route::push('/' . trim($name, '/'));
    $fn();
    Route::pop();
}

function routeParam(string $param, callable $fn): void
{
    Route::push('/' . ltrim($param, '/'));
    $fn();
    Route::pop();
}

function get(string|callable $pathOrFn, ?callable $fn = null): void    { _route('GET',    $pathOrFn, $fn); }
function post(string|callable $pathOrFn, ?callable $fn = null): void   { _route('POST',   $pathOrFn, $fn); }
function patch(string|callable $pathOrFn, ?callable $fn = null): void  { _route('PATCH',  $pathOrFn, $fn); }
function delete(string|callable $pathOrFn, ?callable $fn = null): void { _route('DELETE', $pathOrFn, $fn); }

function _route(string $method, string|callable $pathOrFn, ?callable $fn): void
{
    if (is_callable($pathOrFn)) {
        Route::addRoute($method, $pathOrFn);
    } else {
        Route::push('/' . trim($pathOrFn, '/'));
        Route::addRoute($method, $fn);
        Route::pop();
    }
}

function param(string $name): ?string { return Route::param($name); }
function attributes(): array          { return Route::attributes(); }
function pageParams(): array          { return Route::pageParams(); }

function paginationLinks(string $base, int $number, int $last, int $size): array
{
    return Route::paginationLinks($base, $number, $last, $size);
}

function notFound(string $message = 'Not found.'): never   { Route::notFound($message); }
function unprocessable(array $errors): never               { Route::unprocessable($errors); }
function conflict(string $message): never                  { Route::conflict($message); }
