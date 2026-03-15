<?php

abstract class Endpoint
{
    private array $routes    = [];
    private array $stack     = []; // prefix segment stack
    private array $urlParams = []; // set at dispatch time

    abstract public function register(): void;

    // ── DSL ─────────────────────────────────────────────────────────

    protected function resource(string $name, callable $fn): void
    {
        $this->stack[] = '/' . trim($name, '/');
        $fn();
        array_pop($this->stack);
    }

    protected function routeParam(string $param, callable $fn): void
    {
        $this->stack[] = '/' . ltrim($param, '/');
        $fn();
        array_pop($this->stack);
    }

    protected function get(callable $fn): void    { $this->addRoute('GET',    $fn); }
    protected function post(callable $fn): void   { $this->addRoute('POST',   $fn); }
    protected function patch(callable $fn): void  { $this->addRoute('PATCH',  $fn); }
    protected function delete(callable $fn): void { $this->addRoute('DELETE', $fn); }

    private function addRoute(string $method, callable $fn): void
    {
        $this->routes[] = [
            'method'  => $method,
            'pattern' => implode('', $this->stack) ?: '/',
            'handler' => $fn,
        ];
    }

    public function getRoutes(): array { return $this->routes; }

    // ── Params ──────────────────────────────────────────────────────

    public function setUrlParams(array $params): void { $this->urlParams = $params; }

    protected function param(string $name): ?string { return $this->urlParams[$name] ?? null; }

    // ── Responses ───────────────────────────────────────────────────

    protected function respond(array $data, int $status = 200): never
    {
        header('Content-Type: application/vnd.api+json');
        http_response_code($status);
        echo json_encode(['jsonapi' => ['version' => '1.1']] + $data, JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function noContent(): never
    {
        http_response_code(204);
        exit;
    }

    protected function notFound(string $message = 'Resource not found.'): never
    {
        $this->respond(['errors' => [['status' => '404', 'title' => $message]]], 404);
    }

    protected function unprocessable(array $errors): never
    {
        $this->respond(['errors' => $errors], 422);
    }

    protected function conflict(string $message): never
    {
        $this->respond(['errors' => [['status' => '409', 'title' => $message]]], 409);
    }

    // ── Request helpers ─────────────────────────────────────────────

    protected function attributes(): array
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        return $body['data']['attributes'] ?? [];
    }

    protected function pageParams(): array
    {
        $size   = min(100, max(1, (int) ($_GET['page']['size']   ?? 10)));
        $number = max(1,             (int) ($_GET['page']['number'] ?? 1));
        return ['size' => $size, 'number' => $number, 'offset' => ($number - 1) * $size];
    }

    protected function paginationLinks(string $base, int $number, int $last, int $size): array
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
}
