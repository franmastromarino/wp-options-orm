<?php

namespace QuadLayers\WP_Orm\Tests\Single\Entity;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Tests\Single\SingleEntityTest;

class SingleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $testValues = [
            'key1' => 'new_value_1',
            'key2' => 'new_value_2',
            'key3' => '10',
            'key4' => 10,
            'key5' => 'New Test Alternative',
        ];

        $entity = new SingleEntityTest();

        $entity->set( 'key1', $testValues['key1']);
        $entity->set( 'key2', $testValues['key2']);
        $entity->set( 'key3', $testValues['key3']);
        $entity->set( 'key4', $testValues['key4']);
        $entity->set( 'key5', $testValues['key5']);

        $this->assertEquals($testValues['key1'], $entity->getKey1());
        $this->assertEquals($testValues['key2'], $entity->getKey2());
        $this->assertEquals($testValues['key3'], $entity->getKey3());
        $this->assertEquals($testValues['key4'], $entity->getKey4());
        $this->assertEquals($entity->sanitizeKey5(), $entity->getKey5());
    }

    // Test sanitize function
    public function testSanitize()
    {
        $entity = new SingleEntityTest();

        $entity->setKey1('test');
        $entity->setKey2('test');
        $entity->setKey3('test');

        $this->assertEquals('test', $entity->getKey1());
        $this->assertEquals('test', $entity->getKey2());
    }
}