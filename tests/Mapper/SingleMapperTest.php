<?php

namespace YourNamespace\Tests\Mapper;

use PHPUnit\Framework\TestCase;
use YourNamespace\DTO\SingleDTO;
use YourNamespace\Entity\Single;
use YourNamespace\Mapper\SingleMapper;

class SingleMapperTest extends TestCase
{
    public function testToEntity()
    {
        $dto = new SingleDTO();
        $dto->setKey1('value1');
        $dto->setKey2('value2');

        $mapper = new SingleMapper();

        $entity = $mapper->toEntity($dto);

        $this->assertInstanceOf(Single::class, $entity);
        $this->assertEquals('value1', $entity->getKey1());
        $this->assertEquals('value2', $entity->getKey2());
    }
}
