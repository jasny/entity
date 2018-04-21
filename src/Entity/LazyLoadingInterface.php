<?php

namespace Jasny\Entity;

use Jasny\EntityInterface;

/**
 * Interface for entities that support lazy loading.
 */
interface LazyLoadingInterface extends EntityInterface
{
    /**
     * Check if the object is a ghost.
     * 
     * @return boolean
     */
    public function isGhost(): bool;
    
    /**
     * Expand a ghost.
     * Does nothing is entity isn't a ghost.
     * 
     * @return $this
     */
    public function expand(): self;
}
