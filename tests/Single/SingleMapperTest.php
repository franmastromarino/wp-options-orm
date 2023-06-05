<?php

namespace QuadLayers\WP_Orm\Tests\Single\Mapper;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Entity\SingleEntity;
use QuadLayers\WP_Orm\Entity\EntityInterface;
use QuadLayers\WP_Orm\Factory\SingleFactory;
use QuadLayers\WP_Orm\Mapper\SingleMapper;
use QuadLayers\WP_Orm\Tests\Single\SingleEntityTest;

class SingleMapperTest extends TestCase
{
    private array $testValue;
    private EntityInterface $entity;
    private SingleFactory $factory;
    private SingleMapper $mapper;

    protected function setUp(): void
    {

        $entity = new SingleEntityTest();

        $this->testValue = $entity->getDefaults();

        $this->factory = new SingleFactory('\QuadLayers\WP_Orm\Tests\Single\SingleEntityTest');
        $this->mapper = new SingleMapper($this->factory);
        $this->entity = $this->mapper->toEntity($this->testValue);
    }

    public function testToEntity()
    {

        $entity = $this->mapper->toEntity($this->testValue);

        $this->assertInstanceOf(SingleEntity::class, $entity);
        $this->assertEquals($this->testValue['key1'], $entity->getKey1());
        $this->assertEquals($this->testValue['key2'], $entity->getKey2());
    }

    public function testToArray()
    {

        // Then we convert the entity back to an array
        $array = $this->mapper->toArray($this->entity);

        // Check if the original array and the result are the same
        $this->assertEquals([], $array);
    }

    public function testEntityHasDefaultAttributes()
    {

        $entity = $this->mapper->toEntity([]);

        $this->assertEquals($this->testValue['key1'], $entity->getKey1());
        $this->assertEquals($this->testValue['key2'], $entity->getKey2());
    }
}
