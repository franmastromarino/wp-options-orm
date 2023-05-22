<?php

namespace QuadLayers\WP_Orm\Entity;

interface CollectionInterface
{
    public function get(int $index): SingleInterface;
    public function add(SingleInterface $single): void;
    public function remove(int $index): void;
}
