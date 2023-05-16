<?php

namespace YourNamespace\Tests\DTO;

use PHPUnit\Framework\TestCase;
use YourNamespace\DTO\SingleDTO;

class SingleDTOTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $dto = new SingleDTO();
        $dto->setKey1('value1');
        $dto->setKey2('value2');

        $this->assertEquals('value1', $dto->getKey1());
        $this->assertEquals('value2', $dto->getKey2());
    }
}
