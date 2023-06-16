<?php

namespace QuadLayers\WP_Orm\Entity;

use function QuadLayers\WP_Orm\Helpers\arrayRecursiveDiff;
use function QuadLayers\WP_Orm\Helpers\getObjectVars;

abstract class SingleEntity implements EntityInterface
{
    /**
     * @var array|null
     */
    private $defaults = null;

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

    public function __get(string $key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }

        throw new \InvalidArgumentException("Property '{$key}' does not exist.");
    }

    public function __set(string $key, $value): void
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        } else {
            throw new \InvalidArgumentException("Property '{$key}' does not exist.");
        }
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
