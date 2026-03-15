<?php

require_once APP_ROOT . '/app/api/serializers/Serializer.php';

class SubscriberSerializer extends Serializer
{
    protected string $type = 'subscribers';

    protected static array $attributes = ['email', 'createdAt'];
}
