<?php

namespace Jasny\Support;

use Jasny\EntityInterface;
use Jasny\Entity;

/**
 * @ignore
 */
class IdentifyTestEntity implements EntityInterface
{
    use Entity\Traits\SetStateTrait,
        Entity\Traits\GetSetTrait,
        Entity\Traits\TriggerTrait,
        Entity\Traits\IdentifyTrait,
        Entity\Traits\LazyLoadingTrait;

    public $id;

    public function jsonSerialize()
    {
    }

    public function toArray(): array
    {
    }
}
