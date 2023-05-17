<?php

namespace YourNamespace\DTO;

class SingleDTO implements SingleDTOInterface
{
    private array $properties = ['key1' => null, 'key2' => null];

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $this->properties[$key] = $value;
        }
    }

    public function __get($key)
    {
        return array_key_exists($key, $this->properties) ? $this->properties[$key] : null;
    }

    public function __set($key, $value)
    {
        $this->properties[$key] = $value;
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
        return $this->properties;
    }
}
