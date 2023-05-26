<?php

namespace QuadLayers\WP_Orm\Entity;

use QuadLayers\WP_Orm\Validator\SchemaValidator;
use QuadLayers\WP_Orm\Entity\Single;

class SingleFactory
{
    private string $entityClass;

    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    public function create(array $data): Single
    {
        // Create a new instance of the entity
        $entity = new $this->entityClass();

        // Use reflection to get the properties of the class
        $reflection = new \ReflectionClass($entity);

        // Loop through each data item
        foreach ($data as $property => $value) {
            // Check if the entity has the property and if the value is of the same type
            if ($reflection->hasProperty($property) && gettype($value) === gettype($entity->$property)) {
                // Set the value of the property
                $entity->$property = $value;
            }
        }

        return $entity;
    }
}
