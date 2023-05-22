<?php

namespace YourNamespace\Builder;

use YourNamespace\Entity\SingleFactory;
use YourNamespace\Mapper\SingleMapper;
use YourNamespace\Repository\SingleRepository;

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
