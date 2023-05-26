<?php

namespace QuadLayers\WP_Orm\Entity;

class CollectionFactory
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

        // foreach ($data as $property => $value) {
        //     // Check if the entity has the property
        //     if ($reflection->hasProperty($property)) {
        //         $reflectionProperty = $reflection->getProperty($property);
        //         $reflectionProperty->setAccessible(true);

        //         // If the property hasn't been initialized yet, just set the value.
        //         if (!$reflectionProperty->isInitialized($entity)) {
        //             $entity->$property = $value;
        //         }
        //     }
        // }


        return $entity;
    }
}
