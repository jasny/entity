<?php

declare(strict_types=1);

namespace Jasny\Entity\Collection;

use Improved as i;
use Improved\IteratorPipeline\Pipeline;
use Jasny\Entity\EntityInterface;

/**
 * An entity collection that works as an ordered list.
 * @see https://en.wikipedia.org/wiki/List_(abstract_data_type)
 *
 * @template TEntity of EntityInterface
 * @extends AbstractCollection<int,TEntity>
 */
class EntityList extends AbstractCollection
{
    /**
     * Set the entities of the collection.
     *
     * @param iterable<EntityInterface> $entities
     */
    protected function setEntities(iterable $entities): void
    {
        $this->entities = i\iterable_to_array($entities, false);
    }

    /**
     * Add an entity to the set.
     *
     * @phpstan-param TEntity $entity
     */
    public function add(EntityInterface $entity): void
    {
        $this->entities[] = i\type_check($entity, $this->getType());
    }

    /**
     * Remove an entity from the set.
     *
     * @param mixed|EntityInterface $find  Entity id or entity
     * @return void
     */
    public function remove($find): void
    {
        $this->entities = Pipeline::with($this->entities)
            ->filter(fn(EntityInterface $entity) => !$entity->is($find))
            ->values()
            ->toArray();
    }
}
