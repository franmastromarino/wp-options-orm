<?php

namespace QuadLayers\WP_Orm\Mapper;

use QuadLayers\WP_Orm\Entity\SingleInterface;

interface CollectionMapperInterface
{
    public function toEntity(array $data): SingleInterface;
    public function toArray(SingleInterface $single): array;
}
