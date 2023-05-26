<?php

namespace QuadLayers\WP_Orm\Tests\Repository;

use PHPUnit\Framework\TestCase;
use QuadLayers\WP_Orm\Repository\SingleRepositoryInterface;
use QuadLayers\WP_Orm\Tests\Settings;

class SingleRepositoryTest extends TestCase
{
    private SingleRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SingleRepositoryInterface::class);
    }

    public function testSave()
    {

        $settings = new Settings();

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

        $settings = new Settings();

        $testValue = $settings->getDefaults();

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
