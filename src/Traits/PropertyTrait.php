<?php

namespace Jasny\EntityCollection\Traits;

/**
 * Methods to get the values of a property of all entities
 */
trait PropertyTrait
{
    /**
     * @var EntityInterface[]
     */
    protected $entities = [];


    /**
     * Get property of all entities.
     *
     * @param string $property
     * @param bool   $skipNotSet
     * @return array
     */
    public function getAll(string $property, bool $skipNotSet = true): array
    {
        $result = [];

        foreach ($this->entities as $index => $entity) {
            if (!isset($entity->$property) && $skipNotSet) {
                continue;
            }

            $result[$index] = $entity->$property ?? null;
        }

        return $result;
    }

    /**
     * Get property of all entities as associative array with id as key.
     *
     * @param string $property
     * @param bool   $skipNotSet
     * @return array
     */
    public function getAllById(string $property, bool $skipNotSet = true): array
    {
        $result = [];

        foreach ($this->entities as $entity) {
            if (!isset($entity->$property) && $skipNotSet) {
                continue;
            }

            $result[$entity->getId()] = $entity->$property ?? null;
        }

        return $result;
    }

    /**
     * Get unique values for property of all entities.
     *
     * @param string $property
     * @param bool   $flatten   If property is an array, combine arrays
     * @return array
     */
    public function getUnique(string $property, bool $flatten = false): array
    {
        $items = $this->getAll($property, true);

        if ($flatten) {
            $items = array_reduce($items, function ($items, $item) {
                return array_merge($items, is_array($item) ? $item : [$item]);
            }, []);
        }

        $unique = array_unique($items);
        $result = array_values($unique);

        return $result;
    }

    /**
     * Get sum of property values of all entities
     *
     * @param string $property
     * @return int|float
     */
    public function getSumOf(string $property)
    {
        $sum = array_reduce($this->entities, function ($sum, $item) use ($property) {
            return $sum + ($item->$property ?? 0);
        }, 0);

        return $sum;
    }
}
