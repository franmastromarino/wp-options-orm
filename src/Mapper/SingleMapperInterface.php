<?php

namespace YourNamespace\Mapper;

use YourNamespace\Entity\SingleInterface;
use YourNamespace\DTO\SingleDTOInterface;

interface SingleMapperInterface
{
    public function toEntity(SingleDTOInterface $dto): SingleInterface;
    public function toArray(SingleInterface $single): array;
}
