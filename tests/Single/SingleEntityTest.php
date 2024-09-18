<?php

namespace QuadLayers\WP_Orm\Tests\Single;

class SingleEntityTest extends \QuadLayers\WP_Orm\Entity\SingleEntity
{
    public static $sanitizeProperties = [
        'key1' => 'strval',
        'key2' => 'strval',
        'key3' => self::class . '::sanitizeKey3',
        'key4' => 'self::sanitizeKey4',
        'key5' => '$this->sanitizeKey5',
    ];

    public string $key1 = 'default_value_1';
    public string $key2 = 'default_value_2';
    public string $key3 = '3';
    public int $key4 = 3;
    public string $key5 = 'Test Alternative';

    public static function sanitizeKey3($value)
    {
        if (is_numeric($value)) {
            return (string) $value;
        }
        return '0';
    }

    public static function sanitizeKey4($value)
    {
        return (int) $value;
    }

    public function sanitizeKey5($value = null)
    {
        return 'Test Alternative';
    }
}
