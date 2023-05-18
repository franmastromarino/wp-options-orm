<?php

namespace YourNamespace\Mapper;

use YourNamespace\Entity\SingleInterface;

interface SingleMapperInterface
{
    public function toEntity(array $data): SingleInterface;
    public function toArray(SingleInterface $single): array;
}
