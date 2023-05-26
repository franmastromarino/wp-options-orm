<?php

namespace QuadLayers\WP_Orm\Entity;

abstract class Collection extends Single
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}
