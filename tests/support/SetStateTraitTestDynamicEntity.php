<?php

namespace Jasny;

use Jasny\EntityInterface;
use Jasny\Entity;

/**
 * @ignore
 */
class SetStateTraitTestDynamicEntity implements EntityInterface, Entity\WithDynamicProperties
{
    use Entity\SetStateTrait,
        Entity\SetterTrait;
    
    public $foo;
    public $num;
    
    public function jsonSerialize()
    {
    }

    public function toArray(): array
    {
    }
}
