<?php

namespace QuadLayers\WP_Orm\Repository;

use QuadLayers\WP_Orm\Entity\EntityInterface;

interface SingleRepositoryInterface
{
    public function find(): ?EntityInterface;
    public function save(EntityInterface $single): bool;
    public function update(array $data): bool;
    public function delete(): bool;
    public function create(array $data): bool;
    public function getTable(): string;
}
