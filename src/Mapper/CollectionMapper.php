<?php

namespace QuadLayers\WP_Orm\Mapper;

use QuadLayers\WP_Orm\Entity\SingleInterface;
use QuadLayers\WP_Orm\Entity\CollectionFactory;

class CollectionMapper implements CollectionMapperInterface
{
    private CollectionFactory $factory;

    public function __construct(CollectionFactory $factory)
    {
        $this->factory = $factory;
    }

    public function toEntity(array $data): SingleInterface
    {
        return $this->factory->create($data);
    }

    public function toArray(SingleInterface $single): array
    {
        return $single->getProperties();
    }

    // public function toEntityArray(array $dataArray): array
    // {
    //     return array_map([$this, 'toEntity'], $dataArray);
    // }

    // public function toArrayArray(array $entityArray): array
    // {
    //     return array_map([$this, 'toArray'], $entityArray);
    // }
}
