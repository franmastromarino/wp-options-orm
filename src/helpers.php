<?php

namespace QuadLayers\WP_Orm\Helpers;

function isAssociativeArray(array $array): bool
{
    if (array() === $array) {
        return false;
    }
    return array_keys($array) !== range(0, count($array) - 1);
}

/**
 * PHP does not filter out private and protected properties when called from within the same class.
 * So, we've created this function to call get_object_vars outside class scope.
 */
function getObjectVars($object)
{
    $vars = get_object_vars($object);
    if ($vars === false) {
        return [];
    }
    return $vars;
}

function getObjectSchema($properties, $sanitizeProperties = null): array
{
    // Initialize the defaults array
    $schema = [];

    // Iterate over each public property
    foreach ($properties as $propertyName => $value) {
        // Get the type and default value of the property
        $type = gettype($value);
        // Add the property to the schema array
        $schema[$propertyName] = [
            'sanitizeFunction' => $type,
            'default' => $value
        ];

        // Check if sanitizeProperties is set and if the property has a custom sanitization function
        if (is_array($sanitizeProperties) && isset($sanitizeProperties[$propertyName])) {
            $schema[$propertyName]['sanitizeFunction'] = $sanitizeProperties[$propertyName];
        } elseif ($type === 'object' || $type === 'array' && isAssociativeArray($value)) {
            $schema[$propertyName]['properties'] = getObjectSchema((array) $value);
        }
    }
    // Return the schema array
    return $schema;
}

/**
 * Recursively compares two arrays and returns the differences between them.
 *
 * @param array $array1 The first array to compare.
 * @param array $array2 The second array to compare.
 *
 * @return array The differences between the two arrays.
 */
function arrayRecursiveDiff($array1, $array2)
{
    $result = array();

    foreach ($array2 as $key => $value) {
        // Condition 1: Check if the key doesn't exist in $array1 or if the values are not equal
        if (!array_key_exists($key, $array1) || $value !== $array1[$key]) {
            $result[$key] = $value;
        }
        // Condition 2: Check if the value is an array
        elseif (is_array($value)) {
            // Recursively compare arrays
            $recursiveDiff = arrayRecursiveDiff($array1[$key], $value);
            // Check if there are any differences
            if (count($recursiveDiff)) {
                $result[$key] = $recursiveDiff;
            }
        }
        // Condition 3: Check if the value is an object
        elseif (is_object($value)) {
            // Convert objects to arrays and recursively compare
            $recursiveDiff = arrayRecursiveDiff((array)$array1[$key], (array)$value);
            // Check if there are any differences
            if (count($recursiveDiff)) {
                $result[$key] = (object)$recursiveDiff;
            }
        }
    }

    return $result;
}

function getSanitizedData($data, array $schema, bool $strict = false)
{
    if (is_object($data)) {
        $data = (array)$data;
    }

    if (!is_array($data)) {
        throw new \InvalidArgumentException('The data provided should be an array or object');
    }

    $sanitized = [];
    foreach ($schema as $key => $property) {
        if (!isset($data[$key]) || ($strict && gettype($data[$key]) !== $property['sanitizeFunction'])) {
            if (!$strict) {
                // Check if default is set before assigning.
                if (array_key_exists('default', $property)) {
                    $sanitized[$key] = $property['default'];
                }
            }
            continue;
        }

        $value = $data[$key] ?? null;

        switch ($property['sanitizeFunction']) {
            case 'NULL':
                $sanitized[$key] = null;
                break;
            case 'integer':
            case 'double':
            case 'number':
                if (gettype($value) === 'integer' || gettype($value) === 'double') {
                    $sanitized[$key] = $value;
                } else {
                    $sanitizedValue = filter_var($value, FILTER_VALIDATE_FLOAT);
                    if ($sanitizedValue === false) {
                        // Check if default is set before assigning.
                        if (array_key_exists('default', $property)) {
                            $sanitized[$key] = $property['default'];
                        }
                    } else {
                        // Keep the number as an integer if it has no decimal part
                        $sanitized[$key] = floor($sanitizedValue) == $sanitizedValue ? (int)$sanitizedValue : $sanitizedValue;
                    }
                }
                break;
            case 'string':
                $sanitized[$key] = (string) $value;
                break;
            case 'array':
                if (is_array($value) || is_object($value)) {
                    if (isset($property['properties'])) {
                        $sanitized[$key] = (array) getSanitizedData($value, $property['properties'], $strict);
                    } else {
                        $sanitized[$key] = (array) $value;
                    }
                } else {
                    // Check if default is set before assigning.
                    if (array_key_exists('default', $property)) {
                        $sanitized[$key] = $property['default'];
                    }
                }
                break;
            case 'object':
                if (is_object($value) || is_array($value)) {
                    if (isset($property['properties'])) {
                        $sanitized[$key] = (object) getSanitizedData($value, $property['properties'], $strict);
                    } else {
                        $sanitized[$key] = (object) $value;
                    }
                } else {
                    // Check if default is set before assigning.
                    if (array_key_exists('default', $property)) {
                        $sanitized[$key] = $property['default'];
                    }
                }
                break;
                // break;
            case 'boolean':
                if (is_bool($value)) {
                    $sanitized[$key] = $value;
                } elseif (is_string($value)) {
                    // Convert 'true'/'false' strings to corresponding boolean values
                    if (strtolower($value) === 'true' || strtolower($value) === '1') {
                        $sanitized[$key] = true;
                    } elseif (strtolower($value) === 'false' || strtolower($value) === '0') {
                        $sanitized[$key] = false;
                    } else {
                        // Check if default is set before assigning.
                        if (array_key_exists('default', $property)) {
                            $sanitized[$key] = $property['default'];
                        }
                    }
                } else {
                    // Cast non-string values to boolean
                    $sanitized[$key] = (bool)$value;
                }
                break;
            default:
                try {
                    $sanitized[$key] = $property['sanitizeFunction']($value);
                } catch (\Throwable $e) {
                    throw new \InvalidArgumentException("Error sanitizing value for key '{$key}': {$e->getMessage()}");
                }
                break;
        }
    }

    return $sanitized;
}
