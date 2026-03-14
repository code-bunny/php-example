<?php

class Model {
    protected static string $table = '';
    protected static \PDO $db;
    protected array $attributes = [];

    public static function setDb(\PDO $db): void {
        static::$db = $db;
    }

    public function __construct(array $attributes = []) {
        $this->attributes = $attributes;
    }

    public function __get(string $key): mixed {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void {
        $this->attributes[$key] = $value;
    }

    public static function count(): int {
        return (int) static::$db->query("SELECT COUNT(*) FROM " . static::$table)->fetchColumn();
    }

    public static function paginate(int $limit, int $offset, string $order = 'created_at DESC'): array {
        $stmt = static::$db->prepare("SELECT * FROM " . static::$table . " ORDER BY $order LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return array_map(fn($row) => new static($row), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public static function all(): array {
        $stmt = static::$db->query("SELECT * FROM " . static::$table . " ORDER BY created_at DESC");
        return array_map(fn($row) => new static($row), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public static function find(string $id): ?static {
        $stmt = static::$db->prepare("SELECT * FROM " . static::$table . " WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? new static($row) : null;
    }

    public static function where(string $column, mixed $value): array {
        $stmt = static::$db->prepare("SELECT * FROM " . static::$table . " WHERE $column = ?");
        $stmt->execute([$value]);
        return array_map(fn($row) => new static($row), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function save(): bool {
        if (isset($this->attributes['id'])) {
            return $this->update();
        }
        return $this->insert();
    }

    private function insert(): bool {
        $this->attributes['id'] = $this->generateUuid();
        $columns = array_keys($this->attributes);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            static::$table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        return static::$db->prepare($sql)->execute(array_values($this->attributes));
    }

    private function generateUuid(): string {
        $bytes = random_bytes(16);
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }

    private function update(): bool {
        $id = $this->attributes['id'];
        $fields = array_values(array_filter(array_keys($this->attributes), fn($k) => $k !== 'id'));
        $set = implode(', ', array_map(fn($k) => "$k = ?", $fields));
        $sql = "UPDATE " . static::$table . " SET $set WHERE id = ?";
        $values = array_map(fn($k) => $this->attributes[$k], $fields);
        $values[] = $id;
        return static::$db->prepare($sql)->execute($values);
    }

    public function delete(): bool {
        $stmt = static::$db->prepare("DELETE FROM " . static::$table . " WHERE id = ?");
        return $stmt->execute([$this->attributes['id']]);
    }
}
