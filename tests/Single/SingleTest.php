<?php

namespace QuadLayers\WP_Orm\Tests\Single\Entity;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Tests\Single\SingleEntityTest;

class SingleTest extends TestCase
{
    public function testGettersAndSetters()
    {

        $entity = new SingleEntityTest();

        $testValue = $entity->getDefaults();

        $this->assertEquals($testValue['key1'], $entity->getKey1());
        $this->assertEquals($testValue['key2'], $entity->getKey2());
    }
}
