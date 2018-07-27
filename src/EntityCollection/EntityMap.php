<?php

namespace Jasny\EntityCollection;

use Jasny\EntityCollection\AbstractEntityCollection;
use Jasny\EntityInterface;
use BadMethodCallException;
use InvalidArgumentException;
use OutOfBoundsException;

/**
 * An entity collection that works as a map, so a key/value pairs.
 * @see https://en.wikipedia.org/wiki/Associative_array
 */
class EntityMap extends AbstractEntityCollection
{
    /**
     * Sort the entities as string or on a property.
     *
     * @param string $property
     * @param int    $sortFlags
     * @throws BadMethodCallException
     */
    public function sort(string $property = null, int $sortFlags = SORT_REGULAR)
    {
        throw new BadMethodCallException("Map should not be used as ordered list");
    }

    /**
     * Sort the entities as string or on a property.
     *
     * @throws BadMethodCallException
     */
    public function reverse()
    {
        throw new BadMethodCallException("Map should not be used as ordered list");
    }


    /**
     * Check if offset exists
     *
     * @param string $index
     * @return bool
     */
    public function offsetExists($index)
    {
        return isset($this->entities[$index]);
    }

    /**
     * Get the entity of a specific index or find entity in set
     *
     * @param int $index
     * @return Entity
     * @throws OutOfBoundsException
     */
    public function offsetGet($index)
    {
        $this->assertIndex($index, true);

        return $this->entities[$index];
    }

    /**
     * Replace the entity of a specific index
     *
     * @param int             $index
     * @param EntityInterface $entity  Entity or data representation of entity
     * @return void
     */
    public function offsetSet($index, $entity)
    {
        $this->assertEntity($entity);
        $this->assertIndex($index);

        $this->entities[$index] = $entity;
    }

    /**
     * Remove the entity of a specific index
     *
     * @param int $index
     */
    public function offsetUnset($index)
    {
        $this->assertIndex($index, true);

        unset($this->entities[$index]);
    }

    /**
     * Remove an entity from the set
     *
     * @param mixed|EntityInterface $entity
     * @return void
     */
    public function remove($entity): void
    {
        foreach ($this->findEntity($entity) as $index => $cur) {
            unset($this->entities[$index]);
        }
    }

    /**
     * Set the entities
     *
     * @param EntityInterface[]|iterable $entities
     * @return void
     */
    protected function setEntities(iterable $entities): void
    {
        $this->entities = [];

        foreach ($entities as $key => $entity) {
            $this->assertEntity($entity);
            $this->entities[$key] = $entity;
        }
    }

    /**
     * Apply filter to entities
     *
     * @param callable $filter
     * @return static
     */
    protected function applyFilter($filter)
    {
        $filteredSet = clone $this;
        $filteredSet->entities = array_filter($this->entities, $filter);

        return $filteredSet;
    }

    /**
     * Check if index is an integer or exists in collection
     *
     * @param int     $index
     * @param boolean $exists           Index should be checked for existens in collection
     * @throws InvalidArgumentException If index is not numeric
     * @throws OutOfBoundsException     If index does not exist in collection
     */
    protected function assertIndex($index, $exists = false)
    {
        if (!is_int($index)) {
            throw new InvalidArgumentException("Only numeric keys are allowed");
        }

        if ($exists && !isset($this->entities[$index])) {
            throw new OutOfBoundsException("Key $index does not exist in map");
        }
    }
}
