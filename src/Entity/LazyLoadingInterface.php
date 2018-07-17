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
     * Reload with data from persisted storage.
     *
     * @param array $values
     * @return $this
     * @throws BadMethodCallException if entity is not identifiable
     * @throws InvalidArgumentException if data doesn't belong to entity
     */
    public function reload(array $values);
}
