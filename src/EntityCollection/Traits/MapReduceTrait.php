<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\EntityInterface;

/**
 * Map reduce methods for EntityCollection
 *
 * @property EntityInterface[] $entities
 */
trait MapReduceTrait
{
    /**
     * Get an entity from the set by id
     *
     * @param mixed|EntityInterface $entity   Entity id or Entity
     * @return EntityInterface|null
     */
    abstract public function get($entity): ?EntityInterface;

    /**
     * Returns the result of applying a callback to each value
     *
     * @param callable $callback
     * @return array
     */
    public function map(callable $callback): array
    {
        $result = [];

        foreach ($this->entities as $key => $entity) {
            $result[$key] = $callback($entity, $key);
        }

        return $result;
    }

    /**
     * Map items to entity via callback
     *
     * @param iterable $items     One item per entity, mapped by id (not index)
     * @param callable $callback
     * @return array
     */
    public function mapItems(iterable $items, callable $callback): array
    {
        $result = [];

        foreach ($items as $id => $item) {
            $entity = $this->get($id);

            if (!isset($entity)) {
                continue;
            }

            $result[$id] = $callback($this->entities[$id], $item);
        }

        return $result;
    }

    /**
     * Reduce items into a single value via callback
     *
     * @param callable $callback
     * @param mixed    $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->entities, $callback, $initial);
    }
}
