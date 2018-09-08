<?php

namespace Jasny\Entity;

use stdClass;
use Jasny\Entity\EntityInterface;
use Jasny\Entity\Traits;

/**
 * Base class for an entity.
 */
abstract class Entity implements EntityInterface
{
    use Traits\GetSetTrait;
    use Traits\IdentifyTrait;
    use Traits\JsonSerializeTrait;
    use Traits\LazyLoadingTrait;
    use Traits\SetStateTrait;
    use Traits\TriggerTrait;

    /**
     * On object destruction
     */
    public function __destruct()
    {
        $this->trigger('destruct');
    }
}
