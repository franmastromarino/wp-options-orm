<?php

namespace QuadLayers\WP_Orm\Tests\Mapper;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Entity\Single;
use QuadLayers\WP_Orm\Entity\SingleFactory;
use QuadLayers\WP_Orm\Mapper\SingleMapper;
use QuadLayers\WP_Orm\Tests\Settings;

class SingleMapperTest extends TestCase
{
    private array $testValue;
    private Single $entity;
    private SingleFactory $factory;
    private SingleMapper $mapper;

    protected function setUp(): void
    {

        $settings = new Settings();

        $this->testValue = $settings->getDefaults();

        $this->factory = new SingleFactory('\QuadLayers\WP_Orm\Tests\Settings');
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

        $this->assertEquals($this->testValue['key1'], $entity->getKey1());
        $this->assertEquals($this->testValue['key2'], $entity->getKey2());
    }
}
