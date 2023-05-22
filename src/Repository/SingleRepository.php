<?php

namespace QuadLayers\WP_Orm\Repository;

use QuadLayers\WP_Orm\Entity\SingleInterface;
use QuadLayers\WP_Orm\Mapper\SingleMapperInterface;

class SingleRepository implements SingleRepositoryInterface
{
    private SingleMapperInterface $mapper;
    private string $option_key;
    private ?SingleInterface $cache = null;

    public function __construct(SingleMapperInterface $mapper, string $option_key)
    {
        $this->mapper = $mapper;
        $this->option_key = $option_key;
    }

    public function find(): ?SingleInterface
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $data = get_option($this->option_key, null);
        $this->cache = $data ? $this->mapper->toEntity($data) : null;
        return $this->cache;
    }

    public function save(SingleInterface $entity): bool
    {
        $this->cache = $entity;
        return update_option($this->option_key, $this->mapper->toArray($entity));
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
        return delete_option($this->option_key);
    }

    public function create(array $data): bool
    {
        $entity = $this->mapper->toEntity($data);
        return $this->save($entity);
    }
}
