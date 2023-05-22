<?php

namespace QuadLayers\WP_Orm\Tests\Repository;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Tests\TestValues;
use QuadLayers\WP_Orm\Entity\Single;
use QuadLayers\WP_Orm\Repository\SingleRepositoryInterface;

class SingleRepositoryTest extends TestCase
{
    private SingleRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SingleRepositoryInterface::class);
    }

    public function testSave()
    {

        $testValue = TestValues::getValue();

        $this->repository
        ->expects($this->once())
        ->method('create')
        ->with($testValue)
        ->willReturn(true);

        $this->repository->create($testValue);
    }

    public function testFind()
    {

        $testValue = TestValues::getValue();

        $entity = new Single($testValue);

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
