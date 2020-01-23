<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\Entity;

/**
 * Get methods for EntityCollection
 */
trait GetTrait
{
    /**
     * @var Entity[]
     */
    protected $entities = [];


    /**
     * Find an entity by id or reference
     *
     * @param mixed|Entity $entity
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
     * @param mixed|Entity $entity  Entity id or Entity
     * @return bool
     */
    public function contains($entity): bool
    {
        return $this->findEntity($entity)->valid();
    }

    /**
     * Get an entity from the set by id
     *
     * @param mixed|Entity $entity   Entity id or Entity
     * @return Entity
     * @throws \OutOfBoundsException if entity is not in collection
     */
    public function get($entity): Entity
    {
        $found = $this->findEntity($entity)->current();

        if (!$found) {
            $classname = preg_replace('~^.*/~', '', get_class($this));
            throw new \OutOfBoundsException("Entity not found in $classname");
        }

        return $found;
    }
}
