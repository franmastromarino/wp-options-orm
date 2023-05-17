<?php

namespace YourNamespace\Tests\DTO;

use PHPUnit\Framework\TestCase;
use YourNamespace\Tests\TestValues;
use YourNamespace\DTO\SingleDTO;

class SingleDTOTest extends TestCase
{
    public function testGettersAndSetters()
    {

        $test = TestValues::getTest1();

        $dto = new SingleDTO($test);

        $this->assertEquals($test['key1'], $dto->getKey1());
        $this->assertEquals($test['key2'], $dto->getKey2());
    }
}
