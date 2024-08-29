<?php

namespace QuadLayers\WP_Orm\Tests\Single\Entity;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Tests\Single\SingleEntityTest;

class SingleTest extends TestCase
{
    public function testGettersAndSetters()
    {

        $entity = new SingleEntityTest();

        // TODO: Implement setters
        // $entity->setKey1('test');
        // $entity->setKey2('test');
        // $entity->setKey3('test');
        // $entity->setKey4('test');
        // $entity->setKey5('test');

        $testValue = $entity->getDefaults();

        $this->assertEquals($testValue['key1'], $entity->getKey1());
        $this->assertEquals($testValue['key2'], $entity->getKey2());
        $this->assertEquals($testValue['key3'], $entity->getKey3());
        $this->assertEquals($testValue['key4'], $entity->getKey4());
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