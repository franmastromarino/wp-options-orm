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

    /**
     * @var EntityInterface
     */
    private $entity;

    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    public function create(array $data): EntityInterface
    {
        // Create a new instance of the entity
        $this->entity = new $this->entityClass();

        $sanitizedData = $this->getEntitySanitizedData($data);

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

        // Loop through each data item
        foreach ($sanitizedData as $property => $value) {
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

    private function getEntitySanitizedData(array $data): array
    {

        $entitySanitizeProperties = $this->entity->getSanitizeProperties();

        $defaultProperties = $this->entity->getDefaults();

        $entitySanitizedData = [];

        foreach ($defaultProperties as $property => $value) {
            if (isset($data[$property])) {
                if (! empty(isset($entitySanitizeProperties) && isset($entitySanitizeProperties[$property]))) {
                    $sanitizeFunction = $entitySanitizeProperties[$property];
                    if (is_callable($sanitizeFunction)) {
                        $entitySanitizedData[$property] = call_user_func($sanitizeFunction, $data[$property]);
                    } elseif (is_string($sanitizeFunction) && strpos($sanitizeFunction, 'self::') === 0) {
                        $sanitizeFunction = [$this->entityClass, substr($sanitizeFunction, 6)];
                        $entitySanitizedData[$property] = call_user_func($sanitizeFunction, $data[$property]);
                    } elseif (is_string($sanitizeFunction) && strpos($sanitizeFunction, '$this->') === 0) {
                        $sanitizeFunction = [$this->entity, substr($sanitizeFunction, 7)];
                        $entitySanitizedData[$property] = call_user_func($sanitizeFunction, $data[$property]);
                    } else {
                        $entitySanitizedData[$property] = $data[$property]; // fallback
                    }
                } else {
                    $value = getSanitizedData($data, getObjectSchema($this->entity->getDefaults()))[$property];

                    $entitySanitizedData[$property] = getSanitizedData($data, getObjectSchema($this->entity->getDefaults()))[$property];
                }
            }
        }
        return $entitySanitizedData;
    }
}
