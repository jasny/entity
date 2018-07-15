<?php

namespace Jasny\Entity;

/**
 * An entity that can be lazy loaded
 */
interface LazyLoadingInterface
{
    /**
     * Create a ghost object.
     *
     * @param mixed|array $values  Unique ID or values
     * @return Entity\Ghost
     */
    public static function lazyload($values);

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
