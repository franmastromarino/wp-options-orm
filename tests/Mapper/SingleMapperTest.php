<?php

namespace YourNamespace\Tests\Mapper;

use PHPUnit\Framework\TestCase;
use YourNamespace\Tests\TestValues;
use YourNamespace\Entity\Single;
use YourNamespace\Entity\SingleFactory;
use YourNamespace\Mapper\SingleMapper;

class SingleMapperTest extends TestCase
{
    public function testToEntity()
    {
        $testValue = TestValues::getValue();
        $testSchema = TestValues::getSchema();
        
        $factory = new SingleFactory($testSchema);
        $mapper = new SingleMapper($factory);

        $entity = $mapper->toEntity($testValue);

        $this->assertInstanceOf(Single::class, $entity);

        $this->assertEquals($testValue['key1'], $entity->getKey1());
        $this->assertEquals($testValue['key2'], $entity->getKey2());
    }
}
