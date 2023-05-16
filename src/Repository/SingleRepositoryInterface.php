<?php

namespace YourNamespace\Repository;

use YourNamespace\Entity\SingleInterface;

interface SingleRepositoryInterface
{
    public function find(string $id): ?SingleInterface;
    public function save(SingleInterface $single): bool;
}
