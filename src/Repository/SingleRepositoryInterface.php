<?php

namespace QuadLayers\WP_Orm\Repository;

use QuadLayers\WP_Orm\Entity\SingleInterface;

interface SingleRepositoryInterface
{
    public function find(): ?SingleInterface;
    public function save(SingleInterface $single): bool;
    public function update(array $data): bool;
    public function delete(): bool;
    public function create(array $data): bool;
    public function getTable(): string;
}
