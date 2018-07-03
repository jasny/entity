<?php

namespace Jasny;

use stdClass;
use Jasny\EntityInterface;
use Jasny\Entity\Traits;

/**
 * Base class for an entity.
 */
class Entity extends stdClass implements EntityInterface
{
    use Traits\GetSetTrait;
    use Traits\IdentifiableTrait;
    use Traits\JsonSerializeTrait;
    use Traits\LazyLoadingTrait;
    use Traits\SetStateTrait;
    use Traits\TriggerTrait;
}
