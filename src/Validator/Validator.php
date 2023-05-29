<?php

namespace QuadLayers\WP_Orm\Validator;

class Validator
{
    public function sanitize($value, $type, $schema = [])
    {
        switch ($type) {
            case 'integer':
                return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
            case 'string':
                return filter_var($value, FILTER_SANITIZE_STRING);
            case 'double':
                return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'array':
                if (is_object($value)) {
                    $value = (array) $value;
                }
                if ($schema) {
                    return array_map(function ($value) use ($schema) {
                        return $this->sanitize($value, $schema['type'], $schema);
                    }, $value);
                }
                return $value;
            case 'object':
                if (is_array($value)) {
                    $value = (object) $value;
                }
                if ($schema) {
                    return (object) $this->sanitizeArray((array) $value, $schema);
                }
                return $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            default:
                return $value;
        }
    }

    public function sanitizeArray(array $data, array $schema): array
    {
        $sanitizedData = [];

        foreach ($data as $key => $value) {
            if (isset($schema[$key])) {
                $sanitizedData[$key] = $this->sanitize($value, $schema[$key]['type'], $schema[$key]);
            }
        }

        return $sanitizedData;
    }

    public function validate(array $data, array $schema): array
    {
        return $this->sanitizeArray($data, $schema);
    }
}