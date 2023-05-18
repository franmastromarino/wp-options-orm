<?php

namespace YourNamespace\Repository;

use YourNamespace\Entity\SingleFactory;
use YourNamespace\Entity\SingleInterface;
// use YourNamespace\Mapper\SingleMapper;

class SingleRepository implements SingleRepositoryInterface
{
    private SingleFactory $factory;
    // private SingleMapper $mapper;
    private string $option_key;

    public function __construct(SingleFactory $factory, string $option_key)
    {
        $this->factory = $factory;
        // $this->mapper = new SingleMapper($this->factory);
        $this->option_key = $option_key;
    }

    public function find(): ?SingleInterface
    {
        $data = get_option($this->option_key, null);
        return $data ? $this->factory->create($data) : null;
    }

    public function save(array $data): bool
    {
        $single = $this->factory->create($data);
        return update_option($this->option_key, $single->getProperties());
    }
}
