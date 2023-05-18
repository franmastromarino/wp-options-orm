<?php

namespace YourNamespace\Entity;

use YourNamespace\Validator\SchemaValidator;
use YourNamespace\Entity\Single;

class SingleFactory
{
    private SchemaValidator $validator;

    public function __construct(array $schema)
    {
        $this->validator = new SchemaValidator($schema);
    }

    public function create(array $data): Single
    {
        if (!$this->validator->validate($data)) {
            throw new \InvalidArgumentException("Data doesn't match the schema.");
        }

        $defaults = $this->validator->getDefaults();
        $processedData = array_merge($defaults, $data);

        return new Single($processedData);
    }
}
