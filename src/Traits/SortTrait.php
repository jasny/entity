<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\EntityInterface;

/**
 * Sort methods for EntityCollection
 */
trait SortTrait
{
    /**
     * @var EntityInterface[]
     */
    protected $entities = [];

    /**
     * Get the class entities of this collection (must) have.
     *
     * @return string
     */
    abstract public function getEntityClass(): string;


    /**
     * Determine if entity can be case to a string
     *
     * @return bool
     * @throws \BadMethodCallException if it's Entity class doesn't implement __toString
     */
    protected function sortUseToString(): bool
    {
        $entityClass = $this->getEntityClass();

        if (!method_exists($entityClass, '__toString')) {
            throw new \BadMethodCallException("Class {$entityClass} can't be cast to a string; no sort key provided");
        }

        return true;
    }

    /**
     * Sort the entities as string or on a property.
     *
     * @param string $property
     * @param int    $sortFlags  SORT_* constant
     * @return void
     * @throws BadMethodCallException If $property param is null and __toString() method is not implemented in entity class
     */
    public function sort(string $property = null, int $sortFlags = SORT_REGULAR): void
    {
        $index = [];
        $entities = [];
        $useToString = !isset($property) && $this->sortUseToString();

        foreach ($this->entities as $key => $entity) {
            $index[$key] = $useToString ? (string)$entity : ($entity->$property ?? null);
        }

        asort($index, $sortFlags);

        foreach (array_keys($index) as $key) {
            $entities[] = $this->entities[$key];
        }

        $this->entities = $entities;
    }

    /**
     * Reverse the order of the entities.
     *
     * @return void
     */
    public function reverse(): void
    {
        $this->entities = array_reverse($this->entities, false);
    }
}
