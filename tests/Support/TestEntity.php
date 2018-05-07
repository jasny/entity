<?php

namespace Jasny\Support;

use Jasny\EntityInterface;
use Jasny\Entity;

/**
 * @ignore
 */
class TestEntity implements EntityInterface
{
    use Entity\SetStateTrait,
        Entity\SetterTrait;
    
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
