<?php

namespace Jasny;

use stdClass;
use Jasny\EntityInterface;
use Jasny\Entity\SetterTrait;
use Jasny\Entity\SetStateTrait;
use Jasny\Entity\JsonSerializeTrait;

/**
 * Base class for an entity
 */
class Entity extends stdClass implements EntityInterface
{
    use SetterTrait,
        SetStateTrait,
        JsonSerializeTrait;
    
    /**
     * Cast the entity to an associative array.
     * 
     * @return array
     */
    public function toArray()
    {
        return call_user_func('get_object_vars', $this);
    }
}
