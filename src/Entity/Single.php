<?php

namespace YourNamespace\Entity;

class Single implements SingleInterface
{
    private string $key1;
    private string $key2;

    public function getKey1(): string
    {
        return $this->key1;
    }

    public function setKey1(string $key1): void
    {
        $this->key1 = $key1;
    }

    public function getKey2(): string
    {
        return $this->key2;
    }

    public function setKey2(string $key2): void
    {
        $this->key2 = $key2;
    }
}
