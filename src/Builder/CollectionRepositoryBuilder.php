<?php

namespace QuadLayers\WP_Orm\Builder;

use QuadLayers\WP_Orm\Factory\CollectionFactory;
use QuadLayers\WP_Orm\Mapper\CollectionMapper;
use QuadLayers\WP_Orm\Repository\CollectionRepository;

class CollectionRepositoryBuilder
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $primaryKey;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var array|null
     */
    private $defaultEntities;

    /**
     * @var bool|null
     */
    private $autoIncrement = null;

    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function setEntity(string $entityClass): self
    {
        if (!class_exists($entityClass)) {
            throw new \InvalidArgumentException("Class '{$entityClass}' does not exist.");
        }

        $this->entityClass = $entityClass;
        return $this;
    }

    public function setDefaultEntities(array $defaultEntities): self
    {
        $this->defaultEntities = $defaultEntities;
        return $this;
    }

    public function setAutoIncrement(bool $autoIncrement): self
    {
        $this->autoIncrement = $autoIncrement;
        return $this;
    }

    public function setPrimaryKey(): self
    {

        // Check if the entity class has the primaryKey property
        if (!property_exists($this->entityClass, 'primaryKey') || !isset($this->entityClass::$primaryKey)) {
            throw new \InvalidArgumentException("Class '{$this->entityClass}' does not have the property 'primaryKey'.");
        }

        $primaryKey = $this->entityClass::$primaryKey;

        // Check if the entity class has the primaryKey property
        if (!property_exists($this->entityClass, $primaryKey)) {
            throw new \InvalidArgumentException("Class '{$this->entityClass}' does not have the property '{$primaryKey}'.");
        }

        $this->primaryKey = $primaryKey;

        return $this;
    }

    public function getRepository(): CollectionRepository
    {

        if (null === $this->autoIncrement) {
            throw new \InvalidArgumentException("Auto increment is not set.");
        }

        $this->setPrimaryKey();
        $factory = new CollectionFactory($this->entityClass);
        $mapper = new CollectionMapper($factory);
        return new CollectionRepository($mapper, $this->table, $this->primaryKey, $this->autoIncrement, $this->defaultEntities);
    }
}
