<?php

abstract class ApiResource {

    // ── Responses ───────────────────────────────────────────────────

    protected function respond(array $data, int $status = 200): never {
        header('Content-Type: application/vnd.api+json');
        http_response_code($status);
        echo json_encode(['jsonapi' => ['version' => '1.1']] + $data, JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function noContent(): never {
        http_response_code(204);
        exit;
    }

    protected function notFound(string $message = 'Resource not found.'): never {
        $this->respond(['errors' => [['status' => '404', 'title' => $message]]], 404);
    }

    protected function methodNotAllowed(): never {
        $this->respond(['errors' => [['status' => '405', 'title' => 'Method not allowed.']]], 405);
    }

    protected function unprocessable(array $errors): never {
        $this->respond(['errors' => $errors], 422);
    }

    protected function conflict(string $message): never {
        $this->respond(['errors' => [['status' => '409', 'title' => $message]]], 409);
    }

    // ── Request helpers ─────────────────────────────────────────────

    protected function attributes(): array {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        return $body['data']['attributes'] ?? [];
    }

    protected function pageParams(): array {
        $size   = min(100, max(1, (int) ($_GET['page']['size']   ?? 10)));
        $number = max(1, (int) ($_GET['page']['number'] ?? 1));
        return ['size' => $size, 'number' => $number, 'offset' => ($number - 1) * $size];
    }

    protected function paginationLinks(string $base, int $number, int $last, int $size): array {
        $url = fn($n) => "$base?page[number]=$n&page[size]=$size";
        return [
            'self'  => $url($number),
            'first' => $url(1),
            'last'  => $url($last),
            'prev'  => $number > 1     ? $url($number - 1) : null,
            'next'  => $number < $last ? $url($number + 1) : null,
        ];
    }

    // ── Dispatch ────────────────────────────────────────────────────

    public function collection(): void {
        match ($_SERVER['REQUEST_METHOD']) {
            'GET'   => $this->index(),
            'POST'  => $this->create(),
            default => $this->methodNotAllowed(),
        };
    }

    public function member(string $id): void {
        match ($_SERVER['REQUEST_METHOD']) {
            'GET'    => $this->show($id),
            'PATCH'  => $this->update($id),
            'DELETE' => $this->destroy($id),
            default  => $this->methodNotAllowed(),
        };
    }

    // ── Actions — override in subclasses ────────────────────────────

    protected function index(): void             { $this->methodNotAllowed(); }
    protected function show(string $id): void    { $this->methodNotAllowed(); }
    protected function create(): void            { $this->methodNotAllowed(); }
    protected function update(string $id): void  { $this->methodNotAllowed(); }
    protected function destroy(string $id): void { $this->methodNotAllowed(); }

    abstract protected function present(object $record): array;
}
