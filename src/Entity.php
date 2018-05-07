<?php

namespace Jasny;

use stdClass;
use Jasny\EntityInterface;
use Jasny\Entity\{SetterTrait, ToAssocTrait, SetStateTrait, JsonSerializeTrait};

/**
 * Base class for an entity
 */
class Entity extends stdClass implements EntityInterface
{
    use SetterTrait,
        ToAssocTrait,
        SetStateTrait,
        JsonSerializeTrait;
}
