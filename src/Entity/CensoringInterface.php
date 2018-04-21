<?php

namespace Jasny\Entity;

use Jasny\EntityInterface;

/**
 * Entity can censor properties for output
 */
interface CensoringInterface extends EntityInterface
{
    /**
     * Check if a propery is censored
     * 
     * @param string $property
     * @return boolean
     */
    public function hasCensored(string $property): bool;
    
    /**
     * Censor properties from entity.
     * 
     * @param string[] $properties
     * @return $this
     */
    public function without(string ...$properties): self;
    
    /**
     * Censor all except the specified properties.
     * 
     * @param string[] $properties
     * @return $this
     */
    public function withOnly(string ...$properties): self;
}
