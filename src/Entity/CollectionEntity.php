<?php

namespace QuadLayers\WP_Orm\Entity;

use function QuadLayers\WP_Orm\V2\Helpers\arrayRecursiveDiff;
use function QuadLayers\WP_Orm\V2\Helpers\getSanitizeValue;
use function QuadLayers\WP_Orm\V2\Helpers\isValidValue;

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
        if (!property_exists($this, $propertyName)) {
            throw new \InvalidArgumentException("Property '{$propertyName}' does not exist.");
        }

        $sanitizedValue = getSanitizeValue($this, $propertyName, $value);

        $isValid = isValidValue($this, $propertyName, $sanitizedValue);

        if (!$isValid) {
            throw new \Exception(sprintf('Value "%s" is not a valid value for the "%s" property.', $value, $propertyName), 400);
        }

        $this->$propertyName = $sanitizedValue;
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
