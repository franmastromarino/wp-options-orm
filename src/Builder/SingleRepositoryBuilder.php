<?php

namespace QuadLayers\WP_Orm\Builder;

use QuadLayers\WP_Orm\Entity\SingleFactory;
use QuadLayers\WP_Orm\Mapper\SingleMapper;
use QuadLayers\WP_Orm\Repository\SingleRepository;

class SingleRepositoryBuilder
{
    private string $table;
    private string $group;
    private array $schema;

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

    public function setSchema(array $schema): self
    {
        $this->schema = $schema;
        return $this;
    }

    public function getRepository(): SingleRepository
    {
        $factory = new SingleFactory($this->schema);
        $mapper = new SingleMapper($factory);
        return new SingleRepository($mapper, $this->table);
    }
}
