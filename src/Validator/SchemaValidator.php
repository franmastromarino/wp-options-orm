<?php

namespace YourNamespace\Validator;

class SchemaValidator
{
    private array $schema;

    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    public function validate(array $data): bool
    {
        foreach ($this->schema['properties'] as $key => $property) {
            if (!isset($data[$key])) {
                return false;
            }

            if ($property['type'] === 'object' && isset($property['properties'])) {
                $validator = new self($property);
                if (!$validator->validate($data[$key])) {
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
