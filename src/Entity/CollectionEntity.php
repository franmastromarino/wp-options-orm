<?php

namespace QuadLayers\WP_Orm\Entity;

use function QuadLayers\WP_Orm\Helpers\arrayRecursiveDiff;

abstract class CollectionEntity extends SingleEntity
{
    /**
     * @var string
     */
    public static $primaryKey;

    public function getModifiedProperties(): array
    {
        // Get the current state of the object

        $defaults = $this->getDefaults();
        /**
         * Remove the primary key from the defaults array
         * Always assume that the primary key is modified
         */
        if (array_key_exists(static::$primaryKey, $defaults)) {
            unset($defaults[static::$primaryKey]);
        }
        $properties = $this->getProperties();

        // Compare the current state with the initial state
        $modifiedProperties = arrayRecursiveDiff($defaults, $properties);

        // Return the modified properties
        return $modifiedProperties;
    }
}
