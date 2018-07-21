<?php

namespace Jasny\EntityCollection\Traits;

/**
 * Methods to get the values of a property of all entities
 *
 * @property EntityInterface[] $entities
 */
trait PropertyTrait
{
    /**
     * Get property of all entities.
     *
     * @param string $property
     * @param bool   $skipNotSet
     * @return iterable
     */
    public function getAll(string $property, bool $skipNotSet = true): iterable
    {
        foreach ($this->entities as $index => $entity) {
            if (!isset($entity->$property) && $skipNotSet) {
                continue;
            }

            yield $index => $entity->$property ?? null;
        }
    }

    /**
     * Get property of all entities as associative array with id as key.
     *
     * @param string $property
     * @param bool   $skipNotSet
     * @return iterable
     */
    public function getAllById(string $property, bool $skipNotSet = true): iterable
    {
        foreach ($this->entities as $entity) {
            if (!isset($entity->$property) && $skipNotSet) {
                continue;
            }

            yield $entity->getId() => $entity->$property ?? null;
        }
    }

    /**
     * Get unique values for property of all entities.
     *
     * @param string $property
     * @param bool   $flatten   Flatten array
     * @return iterable
     */
    public function getUnique(string $property, bool $flatten = false): iterable
    {
        $items = iterator_to_array($this->getAll($property));

        if ($flatten) {
            $items = array_reduce($items, function($items, $item) {
                return array_merge($items, is_array($item) ? $item : [$item]);
            }, []);
        }

        return array_unique($items);
    }

    /**
     * @param string $property
     */
    public function sum(string $property)
    {

    }
}
