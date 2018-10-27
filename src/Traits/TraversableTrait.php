<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\Entity;

/**
 * Get iterable representation of EntityCollection
 */
trait TraversableTrait
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
     * @return Entity[]
     */
    public function toArray(): array
    {
        return $this->entities;
    }
}
