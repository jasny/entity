<?php

namespace Jasny\Entity;

/**
 * Entity with typecasting
 */
interface WithTypeCasting
{
    /**
     * Cast all properties of the entity.
     * This function must be idempotent.
     * 
     * @return $this
     */
    public function cast();
}
