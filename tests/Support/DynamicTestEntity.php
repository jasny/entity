<?php

namespace Jasny\Support;

use Jasny\EntityInterface;
use Jasny\Entity;
use Jasny\Entity\DynamicInterface;

/**
 * @ignore
 */
class DynamicTestEntity implements EntityInterface, DynamicInterface
{
    use Entity\Traits\SetStateTrait,
        Entity\Traits\GetSetTrait,
        Entity\Traits\TriggerTrait,
        Entity\Traits\IdentifyTrait,
        Entity\Traits\LazyLoadingTrait;

    public $foo;
    public $num = 0;

    public function jsonSerialize()
    {
    }

    public function toArray(): array
    {
    }

    public function __toString()
    {
        return $this->foo;
    }
}
