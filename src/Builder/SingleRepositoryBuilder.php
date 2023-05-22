<?php

namespace QuadLayers\WP_Orm\Builder;

use QuadLayers\WP_Orm\Entity\SingleFactory;
use QuadLayers\WP_Orm\Mapper\SingleMapper;
use QuadLayers\WP_Orm\Repository\SingleRepository;

class SingleRepositoryBuilder
{
    private array $schema;
    private string $optionKey;

    public function setSchema(array $schema): self
    {
        $this->schema = $schema;
        return $this;
    }

    public function setOptionKey(string $optionKey): self
    {
        $this->optionKey = $optionKey;
        return $this;
    }

    public function getRepository(): SingleRepository
    {
        $factory = new SingleFactory($this->schema);
        $mapper = new SingleMapper($factory);
        return new SingleRepository($mapper, $this->optionKey);
    }
}
