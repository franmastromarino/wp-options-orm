<?php

namespace QuadLayers\WP_Orm\Repository;

use QuadLayers\WP_Orm\Entity\EntityInterface;

interface SingleVirtualRepositoryInterface
{
    public function save(EntityInterface $single): EntityInterface;
    public function update(array $data): EntityInterface;
    public function delete(): bool;
    public function create(array $data): EntityInterface;
}
