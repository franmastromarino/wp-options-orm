<?php

namespace QuadLayers\WP_Orm\Entity;

interface EntityInterface
{
    public function get(string $key);
    public function set(string $key, $value): void;
    public function __get(string $key);
    public function __set(string $key, $value): void;
    public function getProperties(): array;
    public function getDefaults(): ?array;
    public function getModifiedProperties(): array;
    public function getSanitizeProperties(): ?array;
    public function getValidateProperties(): ?array;
}
