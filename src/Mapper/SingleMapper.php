<?php

namespace QuadLayers\WP_Orm\Mapper;

use QuadLayers\WP_Orm\Entity\EntityInterface;
use QuadLayers\WP_Orm\Factory\SingleFactory;

class SingleMapper implements SingleMapperInterface
{
    private SingleFactory $factory;

    public function __construct(SingleFactory $factory)
    {
        $this->factory = $factory;
    }

    public function toEntity(array $data): EntityInterface
    {
        return $this->factory->create($data);
    }

    public function toArray(EntityInterface $single): array
    {
        return $single->getModifiedProperties();
    }
}
