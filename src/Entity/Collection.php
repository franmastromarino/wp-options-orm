<?php

namespace QuadLayers\WP_Orm\Entity;

class Collection implements CollectionInterface
{
    /**
     * @var SingleInterface[] 
     */
    private array $items = [];

    public function get(int $index): SingleInterface
    {
        if (!isset($this->items[$index])) {
            throw new \OutOfRangeException('Invalid index');
        }
        return $this->items[$index];
    }

    public function add(SingleInterface $single): void
    {
        $this->items[] = $single;
    }

    public function remove(int $index): void
    {
        if (!isset($this->items[$index])) {
            throw new \OutOfRangeException('Invalid index');
        }
        unset($this->items[$index]);
    }
}
