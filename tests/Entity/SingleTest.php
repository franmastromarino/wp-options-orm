<?php

namespace YourNamespace\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YourNamespace\Entity\Single;
use YourNamespace\Tests\TestValues;

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
