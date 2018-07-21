<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\EntityInterface;

/**
 * Get iterable representation of EntityCollection
 *
 * @property EntityInterface[] $entities
 */
trait IterableTrait
{
    /**
     * Get the iterator for looping through the set
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->entities);
    }

    /**
     * Get the entities as array
     *
     * @return EntityInterface[]
     */
    public function toArray()
    {
        return $this->entities;
    }
}
