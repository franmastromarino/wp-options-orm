<?php

namespace QuadLayers\WP_Orm\Entity;

use function QuadLayers\WP_Orm\V2\Helpers\arrayRecursiveDiff;
use function QuadLayers\WP_Orm\V2\Helpers\getObjectVars;
use function QuadLayers\WP_Orm\V2\Helpers\isValidValue;
use function QuadLayers\WP_Orm\V2\Helpers\getSanitizeValue;

abstract class SingleEntity implements EntityInterface
{
    const PRIVATE_PROPERTIES = [];

    /**
     * @var array|null
     */
    public static $sanitizeProperties = null;

    /**
     * @var array|null
     */
    public static $validateProperties = null;

    /**
     * @var array|null
     */
    private $defaults = null;

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

    public function __get(string $propertyName)
    {
        if (!property_exists($this, $propertyName)) {
            throw new \InvalidArgumentException("Property '{$propertyName}' does not exist.");
        }

        return $this->$propertyName;
    }

    public function __set(string $propertyName, $value): void
    {

        if (!property_exists($this, $propertyName)) {
            throw new \InvalidArgumentException("Property '{$propertyName}' does not exist.");
        }

        $this->$propertyName = $value;
    }

    public function __call($name, $arguments)
    {
        // Get the first 3 characters of the method name
        $methodType = substr($name, 0, 3);
        // Get the property name by removing the first 3 characters from the method name
        $propertyName = lcfirst(substr($name, 3));

        if ($methodType === 'get') {
            return $this->__get($propertyName);
        } elseif ($methodType === 'set') {
            return $this->__set($propertyName, $arguments[0]);
        } else {
            throw new \Exception("Method $name does not exist");
        }
    }

    public function getModifiedProperties(): array
    {
        // Get the current state of the object
        $defaults = $this->getDefaults();

        $properties = $this->getProperties();

        // Compare the current state with the initial state
        $modifiedProperties = arrayRecursiveDiff($defaults, $properties);

        // Return the modified properties
        return $modifiedProperties;
    }

    public function getProperties(): array
    {
        return getObjectVars($this);
    }

    public function getDefaults(): array
    {
        // If defaults have not been set yet
        if ($this->defaults === null) {
            // Initialize the defaults array
            $this->defaults = getObjectVars(new static());
        }

        // Return the defaults array
        return $this->defaults;
    }
}
