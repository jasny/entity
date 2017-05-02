<?php

namespace Jasny\Entity;

/**
 * Interface for entities that support lazy loading.
 */
interface WithLazyLoading
{
    /**
     * Check if the object is a ghost.
     * 
     * @return boolean
     */
    public function isGhost();
    
    /**
     * Expand a ghost.
     * Does nothing is entity isn't a ghost.
     * 
     * @return $this
     */
    public function expand();
}
