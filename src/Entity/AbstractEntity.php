<?php

namespace QuadLayers\WP_Orm\Entity;

use function QuadLayers\WP_Orm\Helpers\getEntitySanitizedData;
use function QuadLayers\WP_Orm\Helpers\validateProperties;

abstract class AbstractEntity
{
    public static $sanitizeProperties = [];
    public static $validateProperties = [];

    public function __construct(array $entityProperties = [])
    {

        // Get all non static properties of the entity
        $entityReflection = new \ReflectionClass($this);
        $entityProperties = $entityReflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        error_log('entityProperties: ' . json_encode($entityProperties, JSON_PRETTY_PRINT));

        $entity_vars = get_object_vars($this);

        // Sanitize each property with sanitizeProperty method else throw an exception
        $sanitizedProperties = getEntitySanitizedData($entityProperties, self::$sanitizeProperties, $this, get_class($this), $entity_vars);
        
        // Validate each property with validateProperty method else throw an exception
        validateProperties($sanitizedProperties, self::$validateProperties, $this, get_class($this));

        foreach ($sanitizedProperties as $property => $value) {
            $this->$property = $value;
        }

        // TODO: call to helpers function getEntitySanitizedData validateProperties when moved to helpers.
        // This will help to sanitize/validate endpotins that have to handle data and not save it anywhere.
    }
}
