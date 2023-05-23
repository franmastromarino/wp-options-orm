<?php

namespace QuadLayers\WP_Orm\Entity;

use QuadLayers\WP_Orm\Validator\SchemaValidator;
use QuadLayers\WP_Orm\Entity\Single;

class SingleFactory
{
    private SchemaValidator $validator;

    public function __construct(array $schema)
    {
        $this->validator = new SchemaValidator($schema);
    }

    public function create(array $data): Single
    {

        $data = $this->validator->getSanitizedData($data);

        $defaults = $this->validator->getDefaults();
        $processedData = array_merge($defaults, $data);

        return new Single($processedData, $defaults);
    }
}
