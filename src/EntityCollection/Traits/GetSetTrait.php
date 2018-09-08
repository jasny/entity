<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\EntityInterface;

/**
 * Get\set methods for EntityCollection
 *
 * @property EntityInterface[] $entities
 * @property string $entityClass
 */
trait GetSetTrait
{
    /**
     * Replace the entity of a specific index
     *
     * @param int             $index
     * @param EntityInterface $entity  Entity or data representation of entity
     * @return void
     */
    abstract public function offsetSet($index, $entity);

    /**
     * Find an entity by id or reference
     *
     * @param mixed|EntityInterface $entity
     * @return Generator
     */
    abstract protected function findEntity($entity);

    /**
     * Add an entity to the set
     *
     * @param EntityInterface $entity
     * @return void
     */
    public function add(EntityInterface $entity): void
    {
        $this->offsetSet(null, $entity);
    }

    /**
     * Check if the entity exists in this set
     *
     * @param mixed|EntityInterface $entity
     * @return boolean
     */
    public function contains($entity)
    {
        return $this->get($entity) !== null;
    }

    /**
     * Get an entity from the set by id
     *
     * @param mixed|EntityInterface $entity   Entity id or Entity
     * @return EntityInterface|null
     */
    public function get($entity): ?EntityInterface
    {
        return $this->findEntity($entity)->current();
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

        $this->entities = array_values($this->entities);
    }
}
