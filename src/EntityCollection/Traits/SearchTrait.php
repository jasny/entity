<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\EntityCollection\EntitySet;
use Jasny\EntityCollection\EntityCollectionInterface;
use Jasny\Entity\EntityInterface;
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
     * Find first entity that passed a filter.
     *
     * @param array|callable $filter
     * @param int|bool       $flag    Strict if filter is an array or FILTER_* contant for a callable
     * @return EntityInterface
     */
    public function find($filter, $flag = 0)
    {

    }

    /**
     * Find an entity by id or reference
     *
     * @param mixed|EntityInterface $entity
     * @return Generator
     */
    protected function findEntity($entity)
    {
        return $entity instanceof EntityInterface ?
            $this->findEntityByRef($entity) :
            $this->findEntityById($entity);
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
