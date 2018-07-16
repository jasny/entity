<?php

namespace Jasny\EntityCollection;

use Jasny\EntityCollection\AbstractEntityCollection;
use Jasny\EntityInterface;

/**
 * An entity collection that works as a set, so unordered with unique items.
 * @see https://en.wikipedia.org/wiki/Set_(abstract_data_type)
 */
class EntitySet extends AbstractEntityCollection
{
    /**
     * Entities mapped by id
     * @var EntityInterface[]
     */
    protected $map = [];

    /**
     * Set the entities
     *
     * @param EntityInterface[]|iterable $entities
     * @return void
     */
    protected function setEntities(iterable $entities): void
    {
        foreach ($entities as $entity) {
            $this->assertEntity($entity);

            $id = $entity->getId();

            if (isset($this->map[$id])) {
                continue;
            }

            $this->entities[] = $entity;
            $this->map[$id] = $entity;
        }
    }

    /**
     * Check if the entity exists in this set
     *
     * @param mixed|EntityInterface $entity
     * @return boolean
     */
    public function contains($entity)
    {
        $id = $entity instanceof EntityInterface ? $entity->getId() : $entity;

        return isset($this->map[$id]);
    }

    /**
     * Return a unique set of entities.
     *
     * @return $this
     */
    public function unique(): self
    {
        return $this;
    }

    /**
     * Get an entity from the set by id
     *
     * @param mixed|EntityInterface $entity   Entity id or Entity
     * @return EntityInterface|null
     */
    public function get($entity): ?EntityInterface
    {
        $id = $entity instanceof EntityInterface ? $entity->getId() : $entity;

        return isset($id) && isset($this->map[$id]) ? $this->map[$id] : null;
    }

    /**
     * Remove an entity from the set
     *
     * @param mixed|EntityInterface $entity   Entity id or Entity
     * @return void
     */
    public function remove($entity): void
    {
        $id = $entity instanceof EntityInterface ? $entity->getId() : $entity;

        if (isset($this->map[$id])) {
            $index = $this->findEntityById($id)->key();
            unset($this->map[$id], $this->entities[$index]);
        }
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

            list($existing) = array_slice($this->entities, $index, 1) + [null];

            if (isset($existing) && $existing->getId() !== $entity->getId()) {
                throw new BadMethodCallException("Can't replace entity in a set by index");
            }

            return;
        }

        $id = $entity->getId();

        if (!isset($this->map[$id])) {
            $this->entities[] = $entity;
            $this->map[$id] = $entity;
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

        $id = $this->entities[$index]->getId();
        unset($this->map[$id], $this->entities[$index]);
    }
}
