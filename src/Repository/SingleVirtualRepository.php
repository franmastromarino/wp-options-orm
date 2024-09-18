<?php

namespace QuadLayers\WP_Orm\Repository;

use QuadLayers\WP_Orm\Entity\EntityInterface;
use QuadLayers\WP_Orm\Mapper\SingleMapperInterface;

class SingleVirtualRepository implements SingleVirtualRepositoryInterface
{
    /**
     * @var SingleMapperInterface
     */
    private $mapper;

    /**
     * @var EntityInterface|null
     */
    private $cache = null;

    public function __construct(SingleMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    public function save(EntityInterface $entity): EntityInterface
    {
        return $this->cache = $entity;
    }

    public function update(array $data): EntityInterface
    {
        $entity = $this->cache;

        if ($entity === null) {
            return false; // Or throw an exception, as you prefer
        }
        // Merge old and new data
        $updatedData = array_merge($entity->getProperties(), $data);
        $updatedEntity = $this->mapper->toEntity($updatedData);
        return $this->save($updatedEntity);
    }

    public function delete(): bool
    {
        $this->cache = null;
        return true;
    }

    public function create(array $data): EntityInterface
    {
        $entity = $this->mapper->toEntity($data);
        return $this->save($entity);
    }
}
