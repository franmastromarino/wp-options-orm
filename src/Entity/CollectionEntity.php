<?php

namespace QuadLayers\WP_Orm\Entity;

use function QuadLayers\WP_Orm\Helpers\arrayRecursiveDiff;

abstract class CollectionEntity extends SingleEntity
{
    const PRIVATE_PROPERTIES = ['primaryKey','allowDelete', 'allowUpdate'];

    /**
     * @var string
     */
    public static $primaryKey;
    /**
     * @var bool
     */
    private $allowDelete = true;
    /**
     * @var bool
     */
    private $allowUpdate = true;

    public function get(string $key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
    }

    public function set(string $key, $value): void
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        }
    }

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

        // Compare the current state with the initial state and get the modified properties
        $modifiedProperties = arrayRecursiveDiff($defaults, $properties);

        // Return the modified properties
        return $modifiedProperties;
    }
}
