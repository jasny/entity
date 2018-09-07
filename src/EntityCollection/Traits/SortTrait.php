<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\EntityCollection\EntitySet;
use Jasny\EntityCollection\EntityCollectionInterface;
use Jasny\Entity\EntityInterface;
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
     * @throws BadMethodCallException If $property param is null and __toString() method is not implemented in entity class
     */
    public function sort(string $property = null, int $sortFlags = SORT_REGULAR)
    {
        $index = [];
        $entities = [];
        $useToString = false;

        if (!isset($property)) {
            if (!method_exists($this->entityClass, '__toString')) {
                throw new BadMethodCallException("Class {$this->entityClass} does not have __toString method, to use it for sorting");
            }

            $useToString = true;
        }

        foreach ($this->entities as $key => $entity) {
            $index[$key] = $useToString ?
                (string)$entity :
                ($entity->$property ?? null);
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
