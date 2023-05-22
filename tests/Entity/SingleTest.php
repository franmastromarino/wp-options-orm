<?php

namespace QuadLayers\WP_Orm\Tests\Entity;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Entity\Single;
use QuadLayers\WP_Orm\Tests\TestValues;

class SingleTest extends TestCase
{
    public function testGettersAndSetters()
    {

        $testValue = TestValues::getValue();

        $entity = new Single($testValue);

        $this->assertEquals($testValue['key1'], $entity->getKey1());
        $this->assertEquals($testValue['key2'], $entity->getKey2());
    }
}
