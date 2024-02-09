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

        // Get the default values of the entity, new static() is used to get the defaults of the child class
        $entityDefaults = $entity->getDefaults();

        $entitySchema = getObjectSchema($entityDefaults);

        $sanitizedData = getSanitizedData($data, $entitySchema);

        // Use reflection to get the properties of the class
        $entityReflection = new \ReflectionClass($entity);

        // Get private properties of the entity
        $entityPrivates = $entity::PRIVATE_PROPERTIES;

        // Check if the entity has private properties and update them
        if (count($entityPrivates) > 0) {
            // Loop through each private property
            foreach ($entityPrivates as $propertyName) {
                if (array_key_exists($propertyName, $data)) {
                    // Set the value of the property
                    $entity->set($propertyName, $data[$propertyName]);
                }
            }
        }

        // Loop through each data item
        foreach ($sanitizedData as $property => $value) {
            $valueType = gettype($value);
            $propertyType = gettype($entity->$property);
            // Check if the entity has the property and if the value is of the same type
            if ($entityReflection->hasProperty($property) && $valueType === $propertyType) {
                // Set the value of the property
                $entity->$property = $value;
            }
        }

        return $entity;
    }
}
