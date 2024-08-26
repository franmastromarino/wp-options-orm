<?php

namespace QuadLayers\WP_Orm\Helpers;

use QuadLayers\WP_Orm\Entity\EntityInterface;

function isAssociativeArray(array $array): bool
{
    if ([] === $array) {
        return false;
    }
    return array_keys($array) !== range(0, count($array) - 1);
}

/**
 * PHP does not filter out private and protected properties when called from within the same class.
 * So, we've created this function to call get_object_vars outside class scope.
 */
function getObjectVars($object): array
{
    $vars = get_object_vars($object);
    if ($vars === false) {
        return [];
    }
    return $vars;
}

function getObjectSchema(array $properties): array
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

/**
 * Recursively compares two arrays and returns the differences between them.
 *
 * @param array $array1 The first array to compare.
 * @param array $array2 The second array to compare.
 *
 * @return array The differences between the two arrays.
 */
function arrayRecursiveDiff(array $array1, array $array2): array
{
    $result = [];

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

function getSanitizedData(array $data, array $schema, bool $strict = false)
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
                throw new \InvalidArgumentException("Unsupported type '{$property['type']}' in schema for key '{$key}'");
        }
    }

    return $sanitized;
}

function entityValidateProperties(array $data, EntityInterface $entity): void
{
    if (empty($entity->getValidateProperties())) {
        return;
    }

    $entityClass = get_class($entity);

    foreach ($data as $propertyName => $value) {
        if (isset($entity->getValidateProperties()[$propertyName])) {
            $validateFunction = $entity->getValidateProperties()[$propertyName];
            if (is_callable($validateFunction)) {
                $validation = call_user_func($validateFunction, $value);
                if (! $validation) {
                    throw new \Exception(sprintf('Input field %s is invalid', $propertyName), 400);
                }
            } elseif (is_string($validateFunction) && strpos($validateFunction, 'self::') === 0) {
                $validateFunction = [$entityClass, substr($validateFunction, 6)];
                $validation = call_user_func($validateFunction, $value);
                if (! $validation) {
                    throw new \Exception(sprintf('Input field %s is invalid', $propertyName), 400);
                }
            } elseif (is_string($validateFunction) && strpos($validateFunction, '$this->') === 0) {
                $validateFunction = [$entity, substr($validateFunction, 7)];
                $validation = call_user_func($validateFunction, $value);
                if (! $validation) {
                    throw new \Exception(sprintf('Input field %s is invalid', $propertyName), 400);
                }
            }
        }
    }
}

function getEntitySanitizedData(array $data, EntityInterface $entity): array
{

    if (empty($entity->getSanitizeProperties())) {
        return $data;
    }

    $entitySanitizedData = [];
    $entityClass = get_class($entity);

    foreach ($data as $propertyName => $value) {
        if (isset($entity->getSanitizeProperties()[$propertyName])) {
            $sanitizeFunction = $entity->getSanitizeProperties()[$propertyName];
            if (is_callable($sanitizeFunction)) {
                $entitySanitizedData[$propertyName] = call_user_func($sanitizeFunction, $value);
            } elseif (is_string($sanitizeFunction) && strpos($sanitizeFunction, 'self::') === 0) {
                $sanitizeFunction = [$entityClass, substr($sanitizeFunction, 6)];
                $entitySanitizedData[$propertyName] = call_user_func($sanitizeFunction, $value);
            } elseif (is_string($sanitizeFunction) && strpos($sanitizeFunction, '$this->') === 0) {
                $sanitizeFunction = [$entity, substr($sanitizeFunction, 7)];
                $entitySanitizedData[$propertyName] = call_user_func($sanitizeFunction, $value);
            } else {
                $entitySanitizedData[$propertyName] = $value; // fallback
            }
        } else {
            $dataValue = array($propertyName => $value);
            $defaultValue = array($propertyName => $value);
            $entitySanitizedData[$propertyName] = getSanitizedData($dataValue, getObjectSchema($defaultValue))[$propertyName];
        }
    }
    return $entitySanitizedData;
}
