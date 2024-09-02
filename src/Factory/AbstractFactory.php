<?php

namespace QuadLayers\WP_Orm\Factory;

use QuadLayers\WP_Orm\Entity\EntityInterface;

use function QuadLayers\WP_Orm\V2\Helpers\getObjectSchema;
use function QuadLayers\WP_Orm\V2\Helpers\getSanitizedData;

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

        // Loop through each data item
        foreach ($sanitizedData as $propertyName => $value) {
            // Set the value of the property
            $entity->set($propertyName, $value);
        }

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

        return $entity;
    }
}
