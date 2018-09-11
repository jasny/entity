<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\EntityInterface;

/**
 * Get iterable representation of EntityCollection
 */
trait IterableTrait
{
    /**
     * @var array
     */
    protected $entities = [];

    /**
     * Get the iterator for looping through the set
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->entities);
    }

    /**
     * Get the entities as array
     *
     * @return EntityInterface[]
     */
    public function toArray(): array
    {
        return $this->entities;
    }
}
