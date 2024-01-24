<?php

namespace QuadLayers\WP_Orm\Repository;

use QuadLayers\WP_Orm\Entity\EntityInterface;

interface CollectionRepositoryInterface
{
    public function findAll(): ?array;
    public function saveAll(array $collection): bool;
    public function find($primaryKeyValue): ?EntityInterface;
    public function update($primaryKeyValue, array $data): ?EntityInterface;
    public function delete($primaryKeyValue): bool;
    public function create(array $data): ?EntityInterface;
    public function getTable(): string;
}
