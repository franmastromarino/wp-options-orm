<?php

namespace YourNamespace\Mapper;

use YourNamespace\DTO\SingleDTOInterface;
use YourNamespace\Entity\SingleInterface;
use YourNamespace\Entity\Single;

class SingleMapper implements SingleMapperInterface
{
    public function toEntity(SingleDTOInterface $dto): SingleInterface
    {
        $properties = [];
        foreach ($dto->getProperties() as $key => $value) {
            $properties[$key] = $value;
        }
    
        return new Single($properties);
    }

    public function toArray(SingleInterface $single): array
    {
        return $single->getProperties();
    }
}
