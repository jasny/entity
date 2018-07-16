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
