<?php

namespace Jasny\Tests\Support;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\Traits;
use Jasny\Entity\DynamicInterface;

/**
 * @ignore
 */
class DynamicTestEntity implements EntityInterface, DynamicInterface
{
    use Traits\SetStateTrait,
        Traits\GetSetTrait,
        Traits\TriggerTrait,
        Traits\IdentifyTrait,
        Traits\LazyLoadingTrait;

    public $foo;
    public $num = 0;

    public function jsonSerialize()
    {
    }

    public function toArray(): array
    {
    }

    public function applyState(array $data)
    {
    }

    public function __toString()
    {
        return $this->foo;
    }
}
