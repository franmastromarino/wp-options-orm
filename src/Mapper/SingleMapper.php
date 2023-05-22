<?php

namespace QuadLayers\WP_Orm\Mapper;

use QuadLayers\WP_Orm\Entity\SingleInterface;
use QuadLayers\WP_Orm\Entity\SingleFactory;

class SingleMapper implements SingleMapperInterface
{
    private SingleFactory $factory;

    public function __construct(SingleFactory $factory)
    {
        $this->factory = $factory;
    }

    public function toEntity(array $data): SingleInterface
    {
        return $this->factory->create($data);
    }

    public function toArray(SingleInterface $single): array
    {
        return $single->getProperties();
    }
}
