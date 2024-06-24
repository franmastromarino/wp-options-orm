<?php

namespace QuadLayers\WP_Orm\Tests\Single;

class SingleEntityTest extends \QuadLayers\WP_Orm\Entity\SingleEntity
{
    public static $sanitizeProperties = [
        'key1' => 'string',
        'key2' => 'string',
    ];
    public string $key1 = 'default_value_1';
    public string $key2 = 'default_value_2';
}
