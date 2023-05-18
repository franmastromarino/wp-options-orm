<?php

namespace YourNamespace\Implementation;

use YourNamespace\Entity\Single;

interface SingleImplementationInterface
{
    public static function getInstance(string $option_key, array $schema): self;
    public function get(): ?Single;
    public function save(array $data): bool;
}
