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

    public static function all(): array {
        $stmt = static::$db->query("SELECT * FROM " . static::$table);
        return array_map(fn($row) => new static($row), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public static function find(int $id): ?static {
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
        $columns = array_keys($this->attributes);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            static::$table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        $stmt = static::$db->prepare($sql);
        $result = $stmt->execute(array_values($this->attributes));
        if ($result) {
            $this->attributes['id'] = (int) static::$db->lastInsertId();
        }
        return $result;
    }

    private function update(): bool {
        $id = $this->attributes['id'];
        $fields = array_filter(array_keys($this->attributes), fn($k) => $k !== 'id');
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
