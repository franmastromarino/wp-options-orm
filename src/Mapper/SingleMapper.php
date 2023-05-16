<?php

namespace YourNamespace\Mapper;

use YourNamespace\DTO\SingleDTOInterface;
use YourNamespace\Entity\SingleInterface;
use YourNamespace\Entity\Single;

class SingleMapper implements SingleMapperInterface
{
    public function toEntity(SingleDTOInterface $dto): SingleInterface
    {
        $entity = new Single();
        $entity->setKey1($dto->getKey1());
        $entity->setKey2($dto->getKey2());

        return $entity;
    }

    public function toArray(SingleInterface $single): array
    {
        return [
            'key1' => $single->getKey1(),
            'key2' => $single->getKey2(),
        ];
    }
}
