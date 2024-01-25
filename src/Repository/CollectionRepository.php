<?php

namespace QuadLayers\WP_Orm\Repository;

use QuadLayers\WP_Orm\Entity\EntityInterface;
use QuadLayers\WP_Orm\Mapper\CollectionMapperInterface;
use QuadLayers\WP_Orm\Entity\Collection;

class CollectionRepository implements CollectionRepositoryInterface
{
    /**
     * @var CollectionMapperInterface
     */
    private $mapper;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $primaryKey;

    /**
     * @var bool
     */
    private $autoIncrement;
    /**
     * @var array|null
     */
    private $defaultEntities;

    /**
     * @var Collection[]|null
     */
    private $cache = null;

    public function __construct(CollectionMapperInterface $mapper, string $table, string $primaryKey, bool $autoIncrement, array $defaultEntities = null)
    {
        $this->mapper = $mapper;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->autoIncrement = $autoIncrement;
        $this->defaultEntities = $defaultEntities;
    }

    private function getPrimaryKeyValue(EntityInterface $entity)
    {
        $primaryKey = $this->primaryKey;

        if (!property_exists($entity, $primaryKey)) {
            throw new \InvalidArgumentException("Primary key '{$primaryKey}' does not exist in the entity.");
        }

        return $entity->$primaryKey;
    }

    private function getAutoIncrement(): int
    {
        $collection = $this->findAll();

        if (empty($collection)) {
            return 0;
        }

        $maxPrimaryKey = max(array_map(function ($entity) {
            return $entity->{$this->primaryKey};
        }, $collection));

        return $maxPrimaryKey + 1;
    }

    private function getEntityIndex($primaryKeyValue): ?int
    {
        $collection = $this->findAll();

        if (!$collection) {
            return null;
        }

        foreach ($collection as $index => $entity) {
            if ($this->getPrimaryKeyValue($entity) === $primaryKeyValue) {
                return $index;
            }
        }

        return null;
    }

    public function findAll(): ?array
    {

        if ($this->cache !== null) {
            return $this->cache;
        }

        // Merge the default entities with the found entities
        $data = get_option($this->table, $this->defaultEntities);

        $this->cache = $data ? array_values(array_map([$this->mapper, 'toEntity'], $data)) : null;

        return $this->cache;
    }

    public function saveAll(array $collection): bool
    {
        $this->cache = $collection;
        $data = array_values(array_map([$this->mapper, 'toArray'], $collection));
        return update_option($this->table, $data);
    }

    public function deleteAll(): bool
    {
        $this->cache = null;
        return delete_option($this->table);
    }

    public function find($primaryKeyValue): ?EntityInterface
    {
        $index = $this->getEntityIndex($primaryKeyValue);

        if ($index === null) {
            return null;
        }

        $collection = $this->findAll();

        if (!isset($collection[$index])) {
            return null;
        }

        return $collection[$index];
    }

    public function create(array $data): ?EntityInterface
    {

        if (!isset($data[$this->primaryKey])) {
            if ($this->autoIncrement) {
                $data[$this->primaryKey] = $this->getAutoIncrement();
            } else {
                throw new \InvalidArgumentException("Primary key '{$this->primaryKey}' is required.");
            }
        }

        $entity = $this->mapper->toEntity($data);

        $primaryKeyValue = $this->getPrimaryKeyValue($entity);

        $index = $this->getEntityIndex($primaryKeyValue);

        if ($index !== null) {
            throw new \InvalidArgumentException("Primary key '{$primaryKeyValue}' already exists in the collection.");
        }

        // Get the collection
        $collection = $this->findAll() ?? [];

        // Add the entity to the collection
        array_push($collection, $entity);

        if (!$this->saveAll($collection)) {
            return null;
        }

        // Save the updated collection
        return $entity;
    }

    public function update($primaryKeyValue, array $data): ?EntityInterface
    {

        $index = $this->getEntityIndex($primaryKeyValue);

        if ($index === null) {
            return null;
        }

        $collection = $this->findAll();

        if (!isset($collection[$index])) {
            return null;
        }

        $entity = $collection[$index];

        $updatedData = array_merge($entity->getProperties(), $data);
        $updatedEntity = $this->mapper->toEntity($updatedData);

        // Update the entity in the collection
        $collection[$index] = $updatedEntity;
        // Save the updated collection
        if (!$this->saveAll($collection)) {
            return null;
        }

        return $updatedEntity;
    }

    public function delete($primaryKeyValue): bool
    {
        $index = $this->getEntityIndex($primaryKeyValue);

        if ($index === null) {
            return false;
        }

        $collection = $this->findAll();

        if (!isset($collection[$index])) {
            return false;
        }

        // Remove the entity from the collection
        unset($collection[$index]);
        // Save the updated collection
        return $this->saveAll($collection);
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
