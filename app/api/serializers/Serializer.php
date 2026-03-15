<?php

abstract class Serializer
{
    protected string $type = '';

    protected static array $attributes   = [];
    protected static array $hasMany      = []; // ['relation' => SerializerClass::class]
    protected static array $belongsTo    = []; // ['relation' => SerializerClass::class]

    // ── Serialization ─────────────────────────────────────────────────────────

    public static function one(object $resource): array
    {
        return (new static)->build($resource);
    }

    public static function many(array $resources): array
    {
        return array_map(fn($r) => (new static)->build($r), $resources);
    }

    // ── Default timestamp transforms (overridable) ────────────────────────────

    protected function createdAt(object $resource): ?string
    {
        return $resource->created_at
            ? (new DateTime($resource->created_at))->format(DateTime::ATOM)
            : null;
    }

    protected function updatedAt(object $resource): ?string
    {
        return $resource->updated_at
            ? (new DateTime($resource->updated_at))->format(DateTime::ATOM)
            : null;
    }

    // ── Internals ─────────────────────────────────────────────────────────────

    private function build(object $resource): array
    {
        $attrs = [];
        foreach (static::$attributes as $name) {
            $attrs[$name] = method_exists($this, $name)
                ? $this->$name($resource)
                : $this->resolveProperty($resource, $name);
        }

        $object = [
            'type'       => $this->type,
            'id'         => (string) $resource->id,
            'attributes' => $attrs,
        ];

        if (!empty(static::$hasMany) || !empty(static::$belongsTo)) {
            $object['relationships'] = $this->buildRelationships($resource);
        }

        $object['links'] = ['self' => $this->selfLink($resource)];

        return $object;
    }

    private function buildRelationships(object $resource): array
    {
        $rels = [];

        foreach (static::$hasMany as $name => $serializerClass) {
            $related = $resource->$name ?? [];
            $type    = (new $serializerClass)->type;
            $rels[$name] = ['data' => array_map(
                fn($r) => ['id' => (string) $r->id, 'type' => $type],
                (array) $related,
            )];
        }

        foreach (static::$belongsTo as $name => $serializerClass) {
            $related = $resource->$name ?? null;
            $type    = (new $serializerClass)->type;
            $rels[$name] = ['data' => $related
                ? ['id' => (string) $related->id, 'type' => $type]
                : null,
            ];
        }

        return $rels;
    }

    protected function selfLink(object $resource): string
    {
        return '/api/v1/' . $this->type . '/' . $resource->id;
    }

    // Resolve attribute name to a resource property.
    // camelCase names are automatically mapped to snake_case (createdAt → created_at).
    private function resolveProperty(object $resource, string $name): mixed
    {
        if (property_exists($resource, $name)) {
            return $resource->$name;
        }

        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        return $resource->$snake ?? null;
    }
}
