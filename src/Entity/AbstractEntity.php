<?php

namespace QuadLayers\WP_Orm\Entity;

use function QuadLayers\WP_Orm\Helpers\getEntitySanitizedData;
use function QuadLayers\WP_Orm\Helpers\entityValidateProperties;

abstract class AbstractEntity extends SingleEntity
{
    public function __construct(array $data = [])
    {
        // Use reflection to get the properties of the class
        $entityReflection = new \ReflectionClass($this);

        $sanitizeProperties = $this->getSanitizeProperties();

        // Get all non static properties of the entity
        $defaultProperties = $this->getDefaults();

        // Sanitize each property with sanitizeProperty method else throw an exception
        $sanitizedProperties = getEntitySanitizedData($data, $sanitizeProperties, $this, $defaultProperties);

        // Validate each property with validateProperty method else throw an exception
        entityValidateProperties($sanitizedProperties, $this);

        // Loop through each data item
        foreach ($sanitizedProperties as $property => $value) {
            $valueType = gettype($value);
            $propertyType = gettype($this->$property);
            // Check if the entity has the property and if the value is of the same type
            if ($entityReflection->hasProperty($property) && $valueType === $propertyType) {
                // Set the value of the property
                $this->$property = $value;
            }
        }
    }
}
