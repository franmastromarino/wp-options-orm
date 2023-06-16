<?php

namespace QuadLayers\WP_Orm\Tests\Collection;

class CollectionEntityTestPrimaryKey extends \QuadLayers\WP_Orm\Entity\CollectionEntity
{
    /**
     * @var string
     */
    public static $primaryKey = 'test';
    public $test = 0;
    public $key1 = 'default_value_1';
    public $key2 = 'default_value_2';
    public $key3 = [
        'key_3_1' => 'default_value_3',
        'key_3_2' => 'default_value_4',
    ];

    public function __construct()
    {
    }
}
