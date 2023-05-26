<?php

namespace QuadLayers\WP_Orm\Tests;

class SettingsCollection extends \QuadLayers\WP_Orm\Entity\Single
{
    public int $id = 0;
    public string $key1 = 'default_value_1';
    public string $key2 = 'default_value_2';
}
