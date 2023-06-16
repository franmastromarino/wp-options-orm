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

function getObjectSchema($properties): array
{
    // Initialize the defaults array
    $schema = [];
    // Iterate over each public property
    foreach ($properties as $propertyName => $default) {
        // Get the type and default value of the property
        $type = gettype($default);
        // Add the property to the schema array
        $schema[$propertyName] = [
            'type' => $type,
            'default' => $default
        ];
        if ($type === 'object') {
            $schema[$propertyName]['properties'] = getObjectSchema((array) $default);
        } elseif ($type === 'array' && isAssociativeArray($default)) {
            $schema[$propertyName]['properties'] = getObjectSchema($default);
        }
    }
    // Return the schema array
    return $schema;
}

function arrayRecursiveDiff($array1, $array2)
{
    $result = array();

    foreach ($array2 as $key => $value) {
        if (array_key_exists($key, $array1)) {
            if (is_array($value)) {
                $recursiveDiff = arrayRecursiveDiff($array1[$key], $value);
                if (count($recursiveDiff)) {
                    $result[$key] = $recursiveDiff;
                }
            } elseif (is_object($value)) {
                $recursiveDiff = arrayRecursiveDiff((array) $array1[$key], (array) $value);
                if (count($recursiveDiff)) {
                    $result[$key] = (object) $recursiveDiff;
                }
            } elseif ($value !== $array1[$key]) {
                $result[$key] = $value;
            }
        } else {
            $result[$key] = $value;
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
        if (!isset($data[$key]) || ($strict && gettype($data[$key]) !== $property['type'])) {
            if (!$strict) {
                // Check if default is set before assigning.
                if (array_key_exists('default', $property)) {
                    $sanitized[$key] = $property['default'];
                }
            }
            continue;
        }

        $value = $data[$key] ?? null;

        switch ($property['type']) {
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
                $sanitized[$key] = htmlentities($value, ENT_QUOTES, 'UTF-8');
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
                throw new \InvalidArgumentException("Unsupported type '{$property['type']}' in schema for key '{$key}'");
        }
    }

    return $sanitized;
}
