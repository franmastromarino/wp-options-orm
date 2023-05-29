<?php

namespace QuadLayers\WP_Orm\Helpers;

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
