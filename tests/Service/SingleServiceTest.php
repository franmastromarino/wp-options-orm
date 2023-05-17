<?php

namespace YourNamespace\Tests\Service;

use PHPUnit\Framework\TestCase;
use YourNamespace\Tests\TestValues;
use YourNamespace\Entity\Single;
use YourNamespace\DTO\SingleDTO;
use YourNamespace\Service\SingleService;
use YourNamespace\Repository\SingleRepositoryInterface;
use YourNamespace\Mapper\SingleMapperInterface;

class SingleServiceTest extends TestCase
{
    private SingleService $service;
    private SingleRepositoryInterface $repository;
    private SingleMapperInterface $mapper;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SingleRepositoryInterface::class);
        $this->mapper = $this->createMock(SingleMapperInterface::class);
        $this->service = new SingleService($this->repository, $this->mapper);
    }

    public function testProcess()
    {
        $dto = new SingleDTO(TestValues::getTest1());

        $entity = new Single();

        $this->mapper
        ->expects($this->once())
        ->method('toEntity')
        ->with($this->equalTo($dto))
        ->willReturn($entity);

        $this->repository
        ->expects($this->once())
        ->method('save')
        ->with($entity);

        $this->service->process($dto);
    }
}
