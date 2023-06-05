<?php

namespace QuadLayers\WP_Orm\Tests\Collection\Mapper;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Entity\CollectionEntity;
use QuadLayers\WP_Orm\Entity\EntityInterface;
use QuadLayers\WP_Orm\Factory\CollectionFactory;
use QuadLayers\WP_Orm\Mapper\CollectionMapper;
use QuadLayers\WP_Orm\Tests\Collection\CollectionEntityTest;

class CollectionMapperTest extends TestCase
{
    private array $testValue;
    private EntityInterface $entity;
    private CollectionFactory $factory;
    private CollectionMapper $mapper;

    protected function setUp(): void
    {

        $entity = new CollectionEntityTest();

        $this->testValue = $entity->getDefaults();

        $this->factory = new CollectionFactory('\QuadLayers\WP_Orm\Tests\Collection\CollectionEntityTest');
        $this->mapper = new CollectionMapper($this->factory);
        $this->entity = $this->mapper->toEntity($this->testValue);
    }

    public function testToEntity()
    {

        $entity = $this->mapper->toEntity($this->testValue);

        $this->assertInstanceOf(CollectionEntity::class, $entity);
        $this->assertEquals($this->testValue['key1'], $entity->getKey1());
        $this->assertEquals($this->testValue['key2'], $entity->getKey2());
    }

    public function testToArray()
    {

        // Then we convert the entity back to an array
        $array = $this->mapper->toArray($this->entity);

        // Check if the original array and the result are the same
        $this->assertEquals(['id' => 0], $array);
    }

    public function testEntityHasDefaultAttributes()
    {

        $entity = $this->mapper->toEntity([]);

        $this->assertEquals($this->testValue['key1'], $entity->getKey1());
        $this->assertEquals($this->testValue['key2'], $entity->getKey2());
    }
}
