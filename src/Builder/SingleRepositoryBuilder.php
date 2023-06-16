<?php

namespace QuadLayers\WP_Orm\Builder;

use QuadLayers\WP_Orm\Factory\SingleFactory;
use QuadLayers\WP_Orm\Mapper\SingleMapper;
use QuadLayers\WP_Orm\Repository\SingleRepository;

class SingleRepositoryBuilder
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $entityClass;

    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function setGroup(string $group): self
    {
        $this->group = $group;
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

    public function getRepository(): SingleRepository
    {
        $factory = new SingleFactory($this->entityClass);
        $mapper = new SingleMapper($factory);
        return new SingleRepository($mapper, $this->table);
    }
}
