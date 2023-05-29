<?php

namespace QuadLayers\WP_Orm\Entity;

use function QuadLayers\WP_Orm\Helpers\getObjectVars;

abstract class SingleEntity implements EntityInterface
{
    private ?array $defaults = null;
    private ?array $schema = null;

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

    public function getSchema(): array
    {
        // If defaults have not been set yet
        if ($this->schema === null) {
            // Initialize the defaults array
            $this->schema = [];
            // Get the public properties of this object
            $properties = $this->getDefaults();
            // Iterate over each public property
            foreach ($properties as $propertyName => $default) {
                // Get the type and default value of the property
                $type = gettype($default);

                // If the type of the property is an object, get its class name
                if ($type === 'object') {
                    $type = get_class($this->$propertyName);
                }

                // Add the property to the schema array
                $this->schema[$propertyName] = [
                    'type' => $type,
                    'default' => $default
                ];
            }
        }

        // Return the schema array
        return $this->schema;
    }
}
