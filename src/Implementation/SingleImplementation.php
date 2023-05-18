<?php

namespace YourNamespace\Implementation;

use YourNamespace\Validator\SchemaValidator;
use YourNamespace\Entity\Single;
use YourNamespace\Mapper\SingleMapper;
use YourNamespace\Repository\SingleRepository;
use YourNamespace\Entity\SingleFactory;

abstract class SingleImplementation implements SingleImplementationInterface
{
    private static array $instances = [];

    private SingleMapper $mapper;
    private SingleRepository $repository;

    protected function __construct(string $option_key, array $schema)
    {
        $this->mapper = new SingleMapper(new SingleFactory($schema));
        $this->repository = new SingleRepository($this->mapper, $option_key);
    }

    public static function getInstance(string $option_key, array $schema): self
    {
        if (!isset(self::$instances[$option_key])) {
            self::$instances[$option_key] = new static($option_key, $schema);
        }

        return self::$instances[$option_key];
    }

    public function get(): ?Single
    {
        return $this->repository->find();
    }

    public function save(array $data): bool
    {
        return $this->repository->create($data);
    }
}
