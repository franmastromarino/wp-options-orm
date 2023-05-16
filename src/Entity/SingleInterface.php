<?php

namespace YourNamespace\Entity;

interface SingleInterface
{
    public function getKey1(): string;
    public function setKey1(string $key1): void;

    public function getKey2(): string;
    public function setKey2(string $key2): void;
}
