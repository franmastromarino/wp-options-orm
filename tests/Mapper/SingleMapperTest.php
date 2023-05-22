<?php

namespace QuadLayers\WP_Orm\Tests\Mapper;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Tests\TestValues;
use QuadLayers\WP_Orm\Entity\Single;
use QuadLayers\WP_Orm\Entity\SingleFactory;
use QuadLayers\WP_Orm\Mapper\SingleMapper;

class SingleMapperTest extends TestCase
{
    private array $testSchema;
    private array $testValue;
    private Single $entity;
    private SingleFactory $factory;
    private SingleMapper $mapper;

    protected function setUp(): void
    {
        $this->testSchema = TestValues::getSchema();
        $this->testValue = TestValues::getValue();

        $this->factory = new SingleFactory($this->testSchema);
        $this->mapper = new SingleMapper($this->factory);
        $this->entity = $this->mapper->toEntity($this->testValue);
    }

    public function testToEntity()
    {

        $entity = $this->mapper->toEntity($this->testValue);

        $this->assertInstanceOf(Single::class, $entity);
        $this->assertEquals($this->testValue['key1'], $entity->getKey1());
        $this->assertEquals($this->testValue['key2'], $entity->getKey2());
    }

    public function testToArray()
    {

        // Then we convert the entity back to an array
        $array = $this->mapper->toArray($this->entity);

        // Check if the original array and the result are the same
        $this->assertEquals($this->testValue, $array);
    }

    public function testEntityHasDefaultAttributes()
    {

        $entity = $this->mapper->toEntity([]);

        $this->assertEquals($this->testSchema['properties']['key1']['default'], $entity->getKey1());
        $this->assertEquals($this->testSchema['properties']['key2']['default'], $entity->getKey2());
    }
}
