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

        // Validate each property with validateProperty method else throw an exception
        $this->validateProperties( $sanitizedData, $this->entity->getValidateProperties() );

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

    private function validateProperties( $properties, $validateProperties ) : void
    {
        if ( ! $validateProperties || empty($validateProperties) ) {
            return;
        }

        foreach ($properties as $propertyName) {
            if (isset($validateProperties[$propertyName])) {
                $validateFunction = $validateProperties[$propertyName];
                if (is_callable($validateFunction)) {
                    $validation = call_user_func($validateFunction, $this->entity->$propertyName);
                    if ( ! $validation ) {
                        throw new \Exception( sprintf( 'Input field %s is invalid', $propertyName ), 400 );
                    }
                } elseif (is_string($validateFunction) && strpos($validateFunction, 'self::') === 0) {
                    $validateFunction = [$this->entityClass, substr($validateFunction, 6)];
                    $validation = call_user_func($validateFunction, $this->entity->$propertyName);
                    if ( ! $validation ) {
                        throw new \Exception( sprintf( 'Input field %s is invalid', $propertyName ), 400 );

                    }
                } elseif (is_string($validateFunction) && strpos($validateFunction, '$this->') === 0) {
                    $validateFunction = [$this->entity, substr($validateFunction, 7)];
                    $validation = call_user_func($validateFunction, $this->entity->$propertyName);
                    if ( ! $validation ) {
                        throw new \Exception( sprintf( 'Input field %s is invalid', $propertyName ), 400 );

                    }
                }
            }
        }
    }

    private function getEntitySanitizedData(array $data): array
    {

        $entitySanitizeProperties = $this->entity->getSanitizeProperties();

        $defaultProperties = $this->entity->getDefaults();

        $entitySanitizedData = [];

        foreach ($defaultProperties as $propertyName => $value) {
            if (isset($data[$propertyName])) {
                if ( isset($entitySanitizeProperties[$propertyName] ) ) {
                    $sanitizeFunction = $entitySanitizeProperties[$propertyName];
                    if (is_callable($sanitizeFunction)) {
                        $entitySanitizedData[$propertyName] = call_user_func($sanitizeFunction, $data[$propertyName]);
                    } elseif (is_string($sanitizeFunction) && strpos($sanitizeFunction, 'self::') === 0) {
                        $sanitizeFunction = [$this->entityClass, substr($sanitizeFunction, 6)];
                        $entitySanitizedData[$propertyName] = call_user_func($sanitizeFunction, $data[$propertyName]);
                    } elseif (is_string($sanitizeFunction) && strpos($sanitizeFunction, '$this->') === 0) {
                        $sanitizeFunction = [$this->entity, substr($sanitizeFunction, 7)];
                        $entitySanitizedData[$propertyName] = call_user_func($sanitizeFunction, $data[$propertyName]);
                    } else {
                        $entitySanitizedData[$propertyName] = $data[$propertyName]; // fallback
                    }
                } else {
                    $dataValue = array($propertyName => $data[$propertyName]);
                    $defaultValue = array($propertyName => $this->entity->getDefaults()[$propertyName]);
                    $entitySanitizedData[$propertyName] = getSanitizedData( $dataValue, getObjectSchema($defaultValue))[$propertyName];
                }
            }
        }
        return $entitySanitizedData;
    }
}
