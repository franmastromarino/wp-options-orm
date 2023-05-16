<?php

namespace YourNamespace\Service;

use YourNamespace\DTO\SingleDTOInterface;

interface SingleServiceInterface
{
    public function process(SingleDTOInterface $single): bool;
}
