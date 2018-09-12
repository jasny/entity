<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\EntityInterface;

/**
 * Get methods for EntityCollection
 */
trait GetTrait
{
    /**
     * @var EntityInterface[]
     */
    protected $entities = [];


    /**
     * Find an entity by id or reference
     *
     * @param mixed|EntityInterface $entity
     * @return \Generator
     */
    protected function findEntity($entity): \Generator
    {
        foreach ($this->entities as $index => $cur) {
            if ($cur->is($entity)) {
                yield $index => $cur;
            }
        }
    }

    /**
     * Check if the entity exists in this set
     *
     * @param mixed|EntityInterface $entity  Entity id or Entity
     * @return bool
     */
    public function contains($entity): bool
    {
        return $this->findEntity($entity)->valid();
    }

    /**
     * Get an entity from the set by id
     *
     * @param mixed|EntityInterface $entity   Entity id or Entity
     * @return EntityInterface
     * @throws \OutOfBoundsException if entity is not in collection
     */
    public function get($entity): EntityInterface
    {
        $found = $this->findEntity($entity)->current();

        if (!$found) {
            throw new \OutOfBoundsException("Entity not found");
        }

        return $found;
    }
}
