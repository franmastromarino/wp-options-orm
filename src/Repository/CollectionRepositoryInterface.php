<?php

namespace QuadLayers\WP_Orm\Repository;

interface CollectionRepositoryInterface
{
    public function findAll(): ?array;
    public function saveAll(array $single): bool;
}
