<?php

namespace Jasny\Entity;

use Jasny\EntityInterface;

/**
 * Entity with typecasting
 */
interface TypeCastingInterface extends EntityInterface
{
    /**
     * Cast all properties of the entity.
     * This function must be idempotent.
     * 
     * @return $this
     */
    public function cast(): self;
}
