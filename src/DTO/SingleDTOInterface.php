<?php

namespace YourNamespace\DTO;

interface SingleDTOInterface
{
    public function __construct(array $data);
    public function __get($key);
    public function __set($key, $value);
    public function getProperties(): array;
}
