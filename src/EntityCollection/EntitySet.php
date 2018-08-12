<?php

namespace Jasny\EntityCollection;

use Jasny\EntityCollection\AbstractEntityCollection;
use Jasny\EntityInterface;
use BadMethodCallException;
use InvalidArgumentException;

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
     * Set the entity class
     *
     * @throws InvalidArgumentException When entity class is not Identifiable
     */
    protected function assertEntityClass()
    {
        $class = $this->entityClass;

        if (!$class::hasIdProperty()) {
            throw new InvalidArgumentException("$class is not an identifiable, can't create a set");
        }

        parent::assertEntityClass();
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
        $this->map = [];

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

            $this->entities = array_values($this->entities);
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

            if (isset($existing)) {
                if ($existing->getId() !== $entity->getId()) {
                    throw new BadMethodCallException("Can't replace entity in a set by index");
                }

                return;
            }
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

        $this->entities = array_values($this->entities);
    }

    /**
     * Apply filter to entities
     *
     * @param callable $filter
     * @return static
     */
    protected function applyFilter($filter)
    {
        $entities = [];
        $map = [];

        foreach ($this->entities as $entity) {
            $keep = $filter($entity);

            if ($keep) {
                $entities[] = $entity;
                $map[$entity->getId()] = $entity;
            }
        }

        $filteredSet = clone $this;
        $filteredSet->entities = $entities;
        $filteredSet->map = $map;

        return $filteredSet;
    }

    /**
     * Sort the entities as string or on a property.
     *
     * @param string $property
     * @param int    $sortFlags
     * @throws BadMethodCallException
     */
    public function sort(string $property = null, int $sortFlags = SORT_REGULAR)
    {
        throw new BadMethodCallException("Set should not be used as ordered list");
    }

    /**
     * Sort the entities as string or on a property.
     *
     * @throws BadMethodCallException
     */
    public function reverse()
    {
        throw new BadMethodCallException("Set should not be used as ordered list");
    }
}
