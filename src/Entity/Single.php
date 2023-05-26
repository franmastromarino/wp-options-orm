<?php

namespace QuadLayers\WP_Orm\Entity;

class Single implements SingleInterface
{
    private array $properties;
    private array $defaults;

    public function __construct(array $data = [])
    {
        // Initialize defaults with current values of properties
        foreach (get_object_vars($this) as $property => $value) {
            $this->defaults[$property] = $value;
        }

        // Set properties with data
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }


    public function __get($key): string
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }

        throw new \InvalidArgumentException("Property '{$key}' does not exist.");
    }

    public function __set($key, $value): void
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        } else {
            throw new \InvalidArgumentException("Property '{$key}' does not exist.");
        }
    }

    /**
     * TODO: remove magic method
     */
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
        $reflect = new \ReflectionClass($this);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        $properties = [];
        foreach ($props as $prop) {
            $propName = $prop->getName();
            $properties[$propName] = $this->$propName;
        }
        return $properties;
    }

    public function getDefaults(): array
    {
        return $this->defaults;
    }
}
