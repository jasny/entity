<?php

namespace Jasny;

use stdClass;
use Jasny\EntityInterface;
use Jasny\Entity;

/**
 * Base class for an entity
 */
class Entity extends stdClass implements EntityInterface
{
    use Entity\SetterTrait,
        Entity\ToArrayTrait,
        Entity\SetStateTrait,
        Entity\JsonSerializeTrait;
}
