<?php

namespace YourNamespace\Repository;

use YourNamespace\Entity\SingleInterface;

interface SingleRepositoryInterface
{
    public function find(): ?SingleInterface;
    public function save(array $single): bool;
}
