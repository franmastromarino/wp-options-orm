<?php

namespace QuadLayers\WP_Orm\Entity;

use function QuadLayers\WP_Orm\Helpers\getEntitySanitizedData;
use function QuadLayers\WP_Orm\Helpers\validateProperties;

abstract class AbstractEntity extends SingleEntity
{
    public function __construct(array $entityProperties = [])
    {
        // Get all non static properties of the entity
        $defaultProperties = get_object_vars($this);

        // Sanitize each property with sanitizeProperty method else throw an exception
        $sanitizedProperties = getEntitySanitizedData($entityProperties, static::$sanitizeProperties, $this, get_class($this), $defaultProperties);
        
        // Validate each property with validateProperty method else throw an exception
        validateProperties($sanitizedProperties, static::$validateProperties, $this, get_class($this));

        foreach ($sanitizedProperties as $property => $value) {
            $this->$property = $value;
        }
    }
}
