<?php

declare(strict_types=1);

namespace Jasny\Entity\Collection;

use Improved as i;
use Jasny\Entity\EntityInterface;

/**
 * An entity collection that works as an ordered set, so with unique items.
 * @see https://en.wikipedia.org/wiki/Set_(abstract_data_type)
 *
 * @template TEntity of EntityInterface
 * @extends AbstractCollection<int,TEntity>
 */
class EntitySet extends AbstractCollection
{
    /**
     * Set the (unique) entities.
     *
     * @param iterable<EntityInterface> $entities
     *
     * @phpstan-param iterable<TEntity> $entities
     */
    protected function setEntities(iterable $entities): void
    {
        $set = [];

        foreach ($entities as $entity) {
            if (i\iterable_has_none($set, fn(EntityInterface $existing) => $existing->is($entity))) {
                $set[] = $entity;
            }
        }

        $this->entities = $set;
    }

    /**
     * Add an entity to the set.
     *
     * @phpstan-param TEntity $entity
     */
    public function add(EntityInterface $entity): void
    {
        i\type_check($entity, $this->getType());

        if ($this->contains($entity)) {
            return;
        }

        $this->entities[] = $entity;
    }

    /**
     * Remove an entity from the set.
     *
     * @param mixed|EntityInterface $find  Entity id or entity
     *
     * @phpstan-param mixed|TEntity $find
     */
    public function remove($find): void
    {
        foreach ($this->entities as $index => $entity) {
            if ($entity->is($find)) {
                unset($this->entities[$index]);
                break;
            }
        }

        $this->entities = array_values($this->entities);
    }
}
