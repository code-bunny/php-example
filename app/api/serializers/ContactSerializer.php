<?php

require_once APP_ROOT . '/app/api/serializers/Serializer.php';

class ContactSerializer extends Serializer
{
    protected string $type = 'contacts';

    protected static array $attributes = ['email', 'message', 'createdAt'];
}
