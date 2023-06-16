<?php

namespace QuadLayers\WP_Orm\Repository;

use QuadLayers\WP_Orm\Entity\EntityInterface;
use QuadLayers\WP_Orm\Mapper\SingleMapperInterface;

class SingleRepository implements SingleRepositoryInterface
{
    /**
     * @var SingleMapperInterface
     */
    private $mapper;

    /**
     * @var string
     */
    private $table;

    /**
     * @var EntityInterface|null
     */
    private $cache = null;

    public function __construct(SingleMapperInterface $mapper, string $table)
    {
        $this->mapper = $mapper;
        $this->table = $table;
    }

    public function find(): ?EntityInterface
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $data = get_option($this->table, null);
        $this->cache = $data ? $this->mapper->toEntity($data) : null;
        return $this->cache;
    }

    public function save(EntityInterface $entity): bool
    {
        $this->cache = $entity;
        $data = $this->mapper->toArray($entity);
        return update_option($this->table, $data);
    }

    public function update(array $data): bool
    {
        $entity = $this->find();
        if ($entity === null) {
            return false; // or throw an exception, as you prefer
        }
        // merge old and new data
        $updatedData = array_merge($entity->getProperties(), $data);
        $updatedEntity = $this->mapper->toEntity($updatedData);
        return $this->save($updatedEntity);
    }

    public function delete(): bool
    {
        $this->cache = null;
        return delete_option($this->table);
    }

    public function create(array $data): bool
    {
        $entity = $this->mapper->toEntity($data);
        return $this->save($entity);
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
