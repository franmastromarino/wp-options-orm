<?php

namespace YourNamespace\Tests\Entity;

use PHPUnit\Framework\TestCase;
use YourNamespace\Entity\Single;
use YourNamespace\Tests\TestValues;

class SingleTest extends TestCase
{
    public function testGettersAndSetters()
    {

        $test = TestValues::getTest1();

        $dto = new Single($test);

        $this->assertEquals($test['key1'], $dto->getKey1());
        $this->assertEquals($test['key2'], $dto->getKey2());
    }
}
