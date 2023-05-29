<?php

namespace QuadLayers\WP_Orm\Entity;

interface EntityInterface
{
    public function __get(string $key);
    public function __set(string $key, $value): void;
    public function getProperties(): array;
    public function getDefaults(): array;
    public function getModifiedProperties(): array;
}
