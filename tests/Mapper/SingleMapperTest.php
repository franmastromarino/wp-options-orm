<?php

namespace YourNamespace\Tests\Mapper;

use PHPUnit\Framework\TestCase;
use YourNamespace\Tests\TestValues;
use YourNamespace\DTO\SingleDTO;
use YourNamespace\Entity\Single;
use YourNamespace\Mapper\SingleMapper;

class SingleMapperTest extends TestCase
{
    public function testToEntity()
    {
        $test = TestValues::getTest1();
        $dto = new SingleDTO(TestValues::getTest1());
        $mapper = new SingleMapper();

        $entity = $mapper->toEntity($dto);

        $this->assertInstanceOf(Single::class, $entity);

        $dto = new Single($test);

        $this->assertEquals($test['key1'], $entity->getKey1());
        $this->assertEquals($test['key2'], $entity->getKey2());
    }
}
