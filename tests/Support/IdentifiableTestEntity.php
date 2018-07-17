<?php

namespace Jasny\Support;

use Jasny\EntityInterface;
use Jasny\Entity;

/**
 * @ignore
 */
class IdentifiableTestEntity implements EntityInterface
{
    use Entity\Traits\SetStateTrait,
        Entity\Traits\GetSetTrait,
        Entity\Traits\TriggerTrait,
        Entity\Traits\IdentifiableTrait,
        Entity\Traits\LazyLoadingTrait;

    public $id;

    public function jsonSerialize()
    {
    }

    public function toArray(): array
    {
    }
}
