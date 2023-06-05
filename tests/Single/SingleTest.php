<?php

namespace QuadLayers\WP_Orm\Tests\Single\Entity;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Tests\Single\SingleEntityTest;

class SingleTest extends TestCase
{
    public function testGettersAndSetters()
    {

        $settings = new SingleEntityTest();

        $testValue = $settings->getDefaults();

        $this->assertEquals($testValue['key1'], $settings->getKey1());
        $this->assertEquals($testValue['key2'], $settings->getKey2());
    }
}
