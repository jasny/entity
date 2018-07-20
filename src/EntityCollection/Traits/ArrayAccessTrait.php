<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\EntityInterface;

/**
 * ArrayAccess implementation for EntityCollection
 *
 * @property EntityInterface[] $entities
 */
trait ArrayAccessTrait
{
    /**
     * Check if index is an integer and not out of bounds.
     *
     * @param int     $index
     * @param boolean $add     Indexed is used for adding an element
     */
    abstract protected function assertIndex($index, $add = false);

    /**
     * Turn input into array of entities
     *
     * @param EntityInterface|mixed $entity
     */
    abstract protected function assertEntity($entity);

    /**
     * Check if offset exists
     *
     * @param int $index
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

        if (isset($index)) {
            $this->assertIndex($index, true);
            $this->entities[$index] = $entity;
        } else {
            $this->entities[] = $entity;
        }
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
