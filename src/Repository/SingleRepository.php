<?php

namespace YourNamespace\Repository;

use YourNamespace\Entity\SingleInterface;
use YourNamespace\Mapper\SingleMapperInterface;

class SingleRepository implements SingleRepositoryInterface
{
    private SingleMapperInterface $mapper;
    private string $option_key;

    public function __construct(SingleMapperInterface $mapper, string $option_key)
    {
        $this->mapper = $mapper;
        $this->option_key = $option_key;
    }

    public function find(): ?SingleInterface
    {
        $data = get_option($this->option_key, null);
        return $data ? $this->mapper->toEntity($data) : null;
    }

    public function save(SingleInterface $single): bool
    {
        $data = $this->mapper->toArray($single);
        return update_option($this->option_key, $data);
    }
}
