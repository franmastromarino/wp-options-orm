<?php

namespace QuadLayers\WP_Orm\Entity;

use function QuadLayers\WP_Orm\Helpers\arrayRecursiveDiff;

abstract class CollectionEntity extends SingleEntity
{
    protected string $primaryKey = 'id';
    public $id = 0;

    public function getModifiedProperties(): array
    {
        // Get the current state of the object

        $defaults = $this->getDefaults();
        /**
         * Remove the primary key from the defaults array
         * Always assume that the primary key is modified
         */
        if (array_key_exists($this->primaryKey, $defaults)) {
            unset($defaults[$this->primaryKey]);
        }
        $properties = $this->getProperties();

        // Compare the current state with the initial state
        $modifiedProperties = arrayRecursiveDiff($defaults, $properties);

        // Return the modified properties
        return $modifiedProperties;
    }
}
