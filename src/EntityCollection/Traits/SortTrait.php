<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\EntityCollection\EntitySet;
use Jasny\EntityCollectionInterface;
use Jasny\EntityInterface;
use Closure;
use BadMethodCallException;
use function Jasny\expect_type;

/**
 * Sort methods for EntityCollection
 *
 * @property EntityInterface[] $entities
 */
trait SortTrait
{
    /**
     * Sort the entities as string or on a property.
     *
     * @param string $property
     * @param int    $sortFlags
     * @return $this
     */
    public function sort(string $property = null, int $sortFlags = SORT_REGULAR)
    {
        $index = [];
        $entities = [];

        foreach ($this->entities as $key => $entity) {
            $index[$key] = $entity->$property ?? null;
        }

        asort($index, $sortFlags);

        foreach (array_keys($index) as $key) {
            $entities[] = $this->entities[$key];
        }

        $this->entities = $entities;

        return $this;
    }

    /**
     * Reverse the order of the entities.
     *
     * @return $this
     */
    public function reverse()
    {
        $this->entities = array_reverse($this->entities);

        return $this;
    }
}
