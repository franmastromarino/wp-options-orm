<?php

namespace QuadLayers\WP_Orm\Entity;

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
        $reflection = new \ReflectionClass($this);
        $reflectionProperties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $properties = [];
        foreach ($reflectionProperties as $property) {
            $propertyName = $property->getName();
            $properties[$propertyName] = $this->$propertyName;
        }
        return $properties;
    }

    public function getDefaults(): array
    {
        // If defaults have not been set yet
        if ($this->defaults === null) {
            // Initialize the defaults array
            $this->defaults = [];

            // Get the public properties of this object
            $reflection = new \ReflectionClass($this);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            // Create a new instance of the class to extract the default values
            $defaultInstance = $reflection->newInstanceWithoutConstructor();

            // Store the initial value of each property in the defaults array
            foreach ($properties as $property) {
                $propertyName = $property->getName();
                $this->defaults[$propertyName] = $property->getValue($defaultInstance);
            }
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
            // Create an instance of ReflectionClass for this object
            $reflection = new \ReflectionClass($this);
            // Get the public properties of this object
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            // Create a new instance of the class to extract the default values
            $defaultInstance = $reflection->newInstanceWithoutConstructor();
            // Iterate over each public property
            foreach ($properties as $property) {
                // Get the property name
                $propertyName = $property->getName();

                // Get the type and default value of the property
                $type = gettype($this->$propertyName);
                $default = $property->getValue($defaultInstance);

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
