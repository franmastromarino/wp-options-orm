<?php

namespace YourNamespace\Service;

use YourNamespace\Entity\SingleInterface;
use YourNamespace\Repository\SingleRepositoryInterface;
use YourNamespace\DTO\SingleDTOInterface;
use YourNamespace\Mapper\SingleMapperInterface;

class SingleService implements SingleServiceInterface
{
    private SingleRepositoryInterface $repository;
    private SingleMapperInterface $mapper;

    public function __construct(SingleRepositoryInterface $repository, SingleMapperInterface $mapper)
    {
        $this->repository = $repository;
        $this->mapper = $mapper;
    }

    public function process(SingleDTOInterface $dto): bool
    {
        // Convert DTO to entity
        $single = $this->mapper->toEntity($dto);

        // Persist the entity
        return $this->repository->save($single);
    }
}
