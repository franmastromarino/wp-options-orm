<?php

namespace QuadLayers\WP_Orm\Builder;

use QuadLayers\WP_Orm\Factory\SingleFactory;
use QuadLayers\WP_Orm\Mapper\SingleMapper;
use QuadLayers\WP_Orm\Repository\SingleVirtualRepository;

class SingleVirtualRepositoryBuilder
{
    /**
     * @var string
     */
    private $entityClass;

    public function setEntity(string $entityClass): self
    {
        if (!class_exists($entityClass)) {
            throw new \InvalidArgumentException("Class '{$entityClass}' does not exist.");
        }

        $this->entityClass = $entityClass;
        return $this;
    }

    public function getRepository(): SingleVirtualRepository
    {
        $factory = new SingleFactory($this->entityClass);
        $mapper = new SingleMapper($factory);
        return new SingleVirtualRepository($mapper);
    }
}
