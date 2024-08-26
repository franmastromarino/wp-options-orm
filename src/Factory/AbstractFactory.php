<?php

namespace QuadLayers\WP_Orm\Factory;

use QuadLayers\WP_Orm\Entity\EntityInterface;

use function QuadLayers\WP_Orm\Helpers\getEntitySanitizedData;
use function QuadLayers\WP_Orm\Helpers\entityValidateProperties;

abstract class AbstractFactory
{
     /**
     * @var string
     */
    private $entityClass;

    /**
     * @var EntityInterface
     */
    private $entity;

    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    public function create(array $data): ?EntityInterface
    {
        // Create a new instance of the entity
        $this->entity = new $this->entityClass();

        // Use reflection to get the properties of the class
        $entityReflection = new \ReflectionClass($this->entity);

        // Get private properties of the entity
        $entityPrivates = $this->entity::PRIVATE_PROPERTIES;

        // Check if the entity has private properties and update them
        if (count($entityPrivates) > 0) {
            // Loop through each private property
            foreach ($entityPrivates as $propertyName) {
                if (array_key_exists($propertyName, $data)) {
                    // Set the value of the property
                    $this->entity->set($propertyName, $data[$propertyName]);
                }
            }
        }

        $sanitizedProperties = getEntitySanitizedData($data, $this->entity);

        entityValidateProperties($sanitizedProperties, $this->entity);

        // Loop through each data item
        foreach ($sanitizedProperties as $property => $value) {
            $valueType = gettype($value);
            $propertyType = gettype($this->entity->$property);
            // Check if the entity has the property and if the value is of the same type
            if ($entityReflection->hasProperty($property) && $valueType === $propertyType) {
                // Set the value of the property
                $this->entity->$property = $value;
            }
        }

        return $this->entity;
    }
}
