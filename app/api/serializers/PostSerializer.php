<?php

require_once APP_ROOT . '/app/api/serializers/Serializer.php';

class PostSerializer extends Serializer
{
    protected string $type = 'posts';

    protected static array $attributes = ['title', 'body', 'createdAt', 'updatedAt'];
}
