<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\EntityCollection\EntitySet;
use Jasny\EntityCollectionInterface;
use Jasny\EntityInterface;
use Closure;
use BadMethodCallException;
use function Jasny\expect_type;

/**
 * Search methods for EntityCollection
 *
 * @property EntityInterface[] $entities
 */
trait SearchTrait
{
    /**
     * Find an entity by id or reference
     *
     * @param mixed|EntityInterface $entity
     * @return Generator
     */
    protected function findEntity($entity)
    {
        return $entity instanceof EntityInterface ? $this->findEntityByRef() : $this->findEntityById();
    }

    /**
     * Find an entity by reference
     *
     * @param EntityInterface $entity
     * @return Generator
     */
    protected function findEntityByRef(EntityInterface $entity)
    {
        $hasId = $entity::hasIdProperty() && $entity->getId() !== null;

        foreach ($this->entities as $index => $cur) {
            if ($cur === $entity || ($hasId && $cur->getId() === $entity->getId())) {
                yield $index => $cur;
            }
        }
    }

    /**
     * Find an entity by id
     *
     * @param mixed $id
     * @return Generator
     */
    protected function findEntityById($id)
    {
        foreach ($this->entities as $index => $cur) {
            if ($cur->getId() === $id) {
                yield $index => $cur;
            }
        }
    }
}
