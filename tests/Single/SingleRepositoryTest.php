<?php

namespace QuadLayers\WP_Orm\Tests\Single\Repository;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Repository\SingleRepositoryInterface;
use QuadLayers\WP_Orm\Tests\Single\SingleEntityTest;

class SingleRepositoryTest extends TestCase
{
    private SingleRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SingleRepositoryInterface::class);
    }

    public function testSave()
    {

        $entity = new SingleEntityTest();

        $testValue = $entity->getDefaults();

        $this->repository
        ->expects($this->once())
        ->method('create')
        ->with($testValue)
        ->willReturn(true);

        $this->repository->create($testValue);
    }

    public function testFind()
    {

        $entity = new SingleEntityTest();

        $this->repository
        ->expects($this->once())
        ->method('find')
        ->with();

        $this->repository
        ->expects($this->once())
        ->method('find')
        ->willReturn($entity);

        $this->repository->find();
    }
}
