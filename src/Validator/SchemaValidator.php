<?php

namespace QuadLayers\WP_Orm\Validator;

class SchemaValidator
{
    private array $schema;

    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    public function getValidData(array $data): array
    {
        $validatedData = [];

        foreach ($this->schema['properties'] as $key => $property) {
            // Check if the key exists in the data array.
            if (!isset($data[$key])) {
                // If the key doesn't exist, and there's a default, use the default value.
                if (isset($property['default'])) {
                    $validatedData[$key] = $property['default'];
                    continue;
                }
                // If the key doesn't exist in the data array, and there's no default, skip this property.
                else {
                    continue;
                }
            }

            // Validate nested objects recursively.
            if ($property['type'] === 'object' && isset($property['properties'])) {
                $validator = new self($property);
                $validatedData[$key] = $validator->getValidData($data[$key]);
            }
            // If the data type is correct, add it to the validated data array.
            elseif (gettype($data[$key]) === $property['type']) {
                $validatedData[$key] = $data[$key];
            }
            // If the data type is not correct, and there's a default, use the default value.
            elseif (isset($property['default'])) {
                $validatedData[$key] = $property['default'];
            }
        }

        return $validatedData;
    }

    public function getSanitizedData(array $data): array
    {
        $sanitizedData = [];

        foreach ($this->schema['properties'] as $key => $property) {
            if (!isset($data[$key])) {
                continue;
            }

            if ($property['type'] === 'object' && isset($property['properties'])) {
                $validator = new self($property);
                $sanitizedData[$key] = $validator->getSanitizedData($data[$key]);
            } else {
                switch ($property['type']) {
                    case 'string':
                        // Using htmlentities to prevent XSS attacks
                        $sanitizedData[$key] = htmlentities($data[$key], ENT_QUOTES, 'UTF-8');
                        break;
                    case 'integer':
                    case 'number':
                        // Ensuring the number is a valid integer or float
                        $sanitizedData[$key] = filter_var($data[$key], FILTER_VALIDATE_INT) ??
                                           filter_var($data[$key], FILTER_VALIDATE_FLOAT);
                        break;
                    case 'boolean':
                        // Converting to boolean
                        $sanitizedData[$key] = filter_var($data[$key], FILTER_VALIDATE_BOOLEAN);
                        break;
                    case 'array':
                        // Recursively sanitize array elements
                        $sanitizedData[$key] = array_map([$this, 'getSanitizedData'], $data[$key]);
                        break;
                    default:
                        // Any unsupported type will be treated as a string by default
                        $sanitizedData[$key] = htmlentities($data[$key], ENT_QUOTES, 'UTF-8');
                }
            }
        }

        return $sanitizedData;
    }

    public function isValidData(array $data): bool
    {
        foreach ($this->schema['properties'] as $key => $property) {
            if (!isset($data[$key])) {
                if (isset($property['default'])) {
                    continue;
                }
                return false;
            }

            if ($property['type'] === 'object' && isset($property['properties'])) {
                $validator = new self($property['properties']);
                if (!$validator->isValidData($data[$key])) {
                    return false;
                }
            } elseif (gettype($data[$key]) !== $property['type']) {
                return false;
            }
        }

        return true;
    }

    public function getDefaults(): array
    {
        $defaults = [];
        foreach ($this->schema['properties'] as $key => $property) {
            if ($property['type'] === 'object' && isset($property['properties'])) {
                $validator = new self($property);
                $defaults[$key] = $validator->getDefaults();
            } elseif (isset($property['default'])) {
                $defaults[$key] = $property['default'];
            }
        }
        return $defaults;
    }
}
