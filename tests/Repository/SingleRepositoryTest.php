<?php

namespace QuadLayers\WP_Orm\Tests\Repository;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Repository\SingleRepositoryInterface;
use QuadLayers\WP_Orm\Tests\SingleEntityTest;

class SingleRepositoryTest extends TestCase
{
    private SingleRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SingleRepositoryInterface::class);
    }

    public function testSave()
    {

        $settings = new SingleEntityTest();

        $testValue = $settings->getDefaults();

        $this->repository
        ->expects($this->once())
        ->method('create')
        ->with($testValue)
        ->willReturn(true);

        $this->repository->create($testValue);
    }

    public function testFind()
    {

        $settings = new SingleEntityTest();

        $this->repository
        ->expects($this->once())
        ->method('find')
        ->with();

        $this->repository
        ->expects($this->once())
        ->method('find')
        ->willReturn($settings);

        $this->repository->find();
    }
}
