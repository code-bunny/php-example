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

    // Returns the column names for this model's table, sourced from db/schema.php.
    // Falls back to an empty array (current behaviour) if the schema file does not exist yet.
    public static function columns(): array {
        return array_keys(static::schemaDefinition());
    }

    // Returns the full column metadata array for a single column, or null if unknown.
    public static function column(string $name): ?array {
        return static::schemaDefinition()[$name] ?? null;
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

    public function delete(): bool {
        $stmt = static::$db->prepare("DELETE FROM " . static::$table . " WHERE id = ?");
        return $stmt->execute([$this->attributes['id']]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function insert(): bool {
        $this->attributes['id'] = $this->generateUuid();

        // Filter to known columns; let MySQL DEFAULT handle timestamps.
        $data = $this->persistableFor('insert');

        $columns      = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            static::$table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        return static::$db->prepare($sql)->execute(array_values($data));
    }

    private function update(): bool {
        $id   = $this->attributes['id'];
        $data = $this->persistableFor('update');  // excludes id, created_at, updated_at

        $set    = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $sql    = "UPDATE " . static::$table . " SET $set WHERE id = ?";
        $values = [...array_values($data), $id];
        return static::$db->prepare($sql)->execute($values);
    }

    // Returns the attributes that should be written to the database.
    // If db/schema.php exists, filters to known columns only; unknown attributes
    // (typos, virtual fields) are silently excluded rather than causing a DB error.
    // Falls back to all attributes when the schema file has not been generated yet.
    private function persistableFor(string $operation): array
    {
        // Columns that the database manages automatically — never written by the app.
        $managed = match ($operation) {
            'insert' => ['created_at', 'updated_at'],
            'update' => ['id', 'created_at', 'updated_at'],
        };

        $known = static::columns();

        $data = empty($known)
            ? $this->attributes                                           // no schema yet — use all
            : array_intersect_key($this->attributes, array_flip($known)); // filter to known columns

        return array_diff_key($data, array_flip($managed));
    }

    // Loads column definitions from db/schema.php and caches them for the process lifetime.
    // The cache is keyed by table name so all model classes share a single file load.
    private static function schemaDefinition(): array
    {
        static $schema = null;
        if ($schema === null) {
            $file   = __DIR__ . '/../../db/schema.php';
            $schema = file_exists($file) ? (require $file) : [];
        }
        return $schema[static::$table] ?? [];
    }

    private function generateUuid(): string {
        $bytes = random_bytes(16);
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($bytes), 4));
    }
}
