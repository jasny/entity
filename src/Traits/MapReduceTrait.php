<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\Entity;

/**
 * Map reduce methods for EntityCollection
 */
trait MapReduceTrait
{
    /**
     * @var array
     */
    protected $entities = [];


    /**
     * Returns the result of applying a callback to each value.
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
     * Map items to entity via callback.
     *
     * @param array    $items     One item per entity, mapped by id (not index)
     * @param callable $callback
     * @return array
     */
    public function mapItems(array $items, callable $callback): array
    {
        $result = [];

        foreach ($this->entities as $entity) {
            $id = $entity->getId();

            if (array_key_exists($id, $items)) {
                $result[$id] = $callback($entity, $items[$id]);
            }
        }

        return $result;
    }

    /**
     * Reduce items into a single value via callback.
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
