<?php

namespace QuadLayers\WP_Orm\Factory;

use QuadLayers\WP_Orm\Entity\EntityInterface;

use function QuadLayers\WP_Orm\Helpers\getObjectSchema;
use function QuadLayers\WP_Orm\Helpers\getSanitizedData;

abstract class AbstractFactory
{
     /**
     * @var string
     */
    private $entityClass;

    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    public function create(array $data): EntityInterface
    {
        // Create a new instance of the entity
        $entity = new $this->entityClass();

        $defaults = $entity->getDefaults();

        $schema = getObjectSchema($defaults);

        $sanitizedData = getSanitizedData($data, $schema);

        // Use reflection to get the properties of the class
        $reflection = new \ReflectionClass($entity);

        // Loop through each data item
        foreach ($sanitizedData as $property => $value) {
            $valueType = gettype($value);
            $propertyType = gettype($entity->$property);
            // Check if the entity has the property and if the value is of the same type
            if ($reflection->hasProperty($property) && $valueType === $propertyType) {
                // Set the value of the property
                $entity->$property = $value;
            }
        }

        return $entity;
    }
}
