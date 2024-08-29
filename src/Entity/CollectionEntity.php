<?php

namespace QuadLayers\WP_Orm\Entity;

use function QuadLayers\WP_Orm\Helpers\arrayRecursiveDiff;
use function QuadLayers\WP_Orm\Helpers\getSanitizeValue;

abstract class CollectionEntity extends SingleEntity
{
    const PRIVATE_PROPERTIES = ['primaryKey','allowDelete', 'allowUpdate', 'sanitizeProperties', 'validateProperties'];

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

    public function get(string $propertyName)
    {
        if (property_exists($this, $propertyName)) {
            return $this->$propertyName;
        }
    }

    public function set(string $propertyName, $value): void
    {
        if (property_exists($this, $propertyName)) {
            $this->$propertyName = $value;
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
