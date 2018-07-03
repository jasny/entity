<?php

namespace Jasny\EntityCollection;

use Jasny\EntityCollection\AbstractEntityCollection;
use Jasny\EntityInterface;
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
     * @return $this
     */
    public function sort($property = null)
    {
        uksort($this->entities, function($a, $b) use($property) {
            $valA = isset($property) ? (isset($a->$property) ? $a->$property : null) : (string)$a;
            $valB = isset($property) ? (isset($b->$property) ? $b->$property : null) : (string)$b;

            return strcmp($valA, $valB);
        });

        return $this;
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
        $this->assertIndex($index);

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
        $this->assertIndex($index, true);

        $this->entities[$index] = $entity;
    }

    /**
     * Remove the entity of a specific index
     *
     * @param int $index
     */
    public function offsetUnset($index)
    {
        $this->assertIndex($index);

        unset($this->entities[$index]);
    }
}
