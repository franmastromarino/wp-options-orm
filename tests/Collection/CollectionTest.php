<?php

namespace QuadLayers\WP_Orm\Tests\Collection\Entity;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Tests\Collection\CollectionEntityTest;

class CollectionTest extends TestCase
{
    public function testGettersAndSetters()
    {

        $entity = new CollectionEntityTest();

        $testValue = $entity->getDefaults();

        $this->assertEquals($testValue['key1'], $entity->getKey1());
        $this->assertEquals($testValue['key2'], $entity->getKey2());
    }
}
