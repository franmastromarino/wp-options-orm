<?php

namespace QuadLayers\WP_Orm\Mapper;

use QuadLayers\WP_Orm\Entity\EntityInterface;

interface CollectionMapperInterface
{
    public function toEntity(array $data): EntityInterface;
    public function toArray(EntityInterface $single): array;
}
