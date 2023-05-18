<?php

namespace YourNamespace\Service;

// use YourNamespace\Entity\SingleInterface;
use YourNamespace\Repository\SingleRepositoryInterface;
use YourNamespace\Mapper\SingleMapperInterface;

class SingleService implements SingleServiceInterface
{
    private SingleRepositoryInterface $repository;
    // private SingleMapperInterface $mapper;

    public function __construct(SingleRepositoryInterface $repository, SingleMapperInterface $mapper)
    {
        $this->repository = $repository;
        // $this->mapper = $mapper;
    }

    public function process(array $data): bool
    {
        // Convert DTO to entity
        // $single = $this->mapper->toEntity($data);

        // Persist the entity
        return $this->repository->save($data);
    }
}
