<?php

namespace Jasny\Support;

use Jasny\EntityInterface;
use Jasny\Entity;

/**
 * @ignore
 */
class DynamicTestEntity implements EntityInterface, Entity\DynamicInterface
{
    use Entity\SetStateTrait,
        Entity\SetterTrait;
    
    public $foo;
    public $num = 0;
    
    public function jsonSerialize()
    {
    }

    public function toArray(): array
    {
    }
}
