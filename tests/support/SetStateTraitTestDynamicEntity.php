<?php

namespace Jasny;

use Jasny\EntityInterface;
use Jasny\Entity\SetStateTrait;

/**
 * @ignore
 */
abstract class SetStateTraitTestEntity implements EntityInterface, Entity\WithDynamicProperties
{
    use SetStateTrait;
    
    public $foo;
    public $num;
}
