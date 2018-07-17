<?php

namespace Jasny\Support;

use Jasny\EntityInterface;
use Jasny\Entity;

/**
 * @ignore
 */
class TestEntity implements EntityInterface
{
    use Entity\Traits\SetStateTrait,
        Entity\Traits\GetSetTrait,
        Entity\Traits\TriggerTrait,
        Entity\Traits\IdentifyTrait,
        Entity\Traits\LazyLoadingTrait;

    public $foo;
    public $num;

    public function __construct()
    {
        $this->num = $this->num ?? 0;
    }

    public function jsonSerialize()
    {
    }

    public function toArray(): array
    {
    }
}
