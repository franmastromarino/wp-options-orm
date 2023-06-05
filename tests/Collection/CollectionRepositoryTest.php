<?php

namespace QuadLayers\WP_Orm\Tests\Collection\Repository;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Repository\CollectionRepositoryInterface;
use QuadLayers\WP_Orm\Tests\Collection\CollectionEntityTest;

class CollectionRepositoryTest extends TestCase
{
    private CollectionRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(CollectionRepositoryInterface::class);
    }

    public function testSave()
    {

        $entity = new CollectionEntityTest();

        $testValue = $entity->getDefaults();

        $this->repository
        ->expects($this->once())
        ->method('create')
        ->with($testValue)
        ->willReturn($entity);

        $this->repository->create($testValue);
    }

    public function testFind()
    {

        $entity = new CollectionEntityTest();

        $this->repository
        ->expects($this->once())
        ->method('find')
        ->with();

        $this->repository
        ->expects($this->once())
        ->method('find')
        ->willReturn($entity);

        $this->repository->find(0);
    }
}
