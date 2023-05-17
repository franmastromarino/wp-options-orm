<?php

namespace YourNamespace\Repository;

use YourNamespace\Entity\SingleInterface;
use YourNamespace\Mapper\SingleMapperInterface;

class SingleRepository implements SingleRepositoryInterface
{
    private SingleMapperInterface $mapper;

    public function __construct(SingleMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    public function find(string $id): ?SingleInterface
    {
        // Load data from your data source here. This is just an example.
        $data = get_option($id, null);
        return $data ? $this->mapper->toEntity($data) : null;
    }

    public function save(SingleInterface $single): bool
    {
        // Save data to your data source here. This is just an example.
        $data = $this->mapper->toArray($single);

        return update_option($single->getKey1(), $data);
    }
}
