<?php

namespace QuadLayers\WP_Orm\Mapper;

use QuadLayers\WP_Orm\Entity\EntityInterface;
use QuadLayers\WP_Orm\Factory\CollectionFactory;

class CollectionMapper implements CollectionMapperInterface
{
     /**
     * @var CollectionFactory
     */
    private $factory;

    public function __construct(CollectionFactory $factory)
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
