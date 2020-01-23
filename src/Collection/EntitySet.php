<?php

declare(strict_types=1);

namespace Jasny\Entity\Collection;

use Improved as i;
use Improved\IteratorPipeline\Pipeline;
use Jasny\Entity\IdentifiableEntityInterface;

/**
 * An entity collection that works as an ordered set, so with unique items.
 * @see https://en.wikipedia.org/wiki/Set_(abstract_data_type)
 *
 * @template TEntity of IdentifiableEntityInterface
 * @extends AbstractCollection<int,TEntity>
 */
class EntitySet extends AbstractCollection
{
    /**
     * Class constructor
     *
     * @param string|null $type  Class name of entities in the collection
     * @throws \InvalidArgumentException if entity class is not an identifiable entity
     *
     * @phpstan-param class-string<TEntity> $type
     */
    public function __construct(string $type = IdentifiableEntityInterface::class)
    {
        if (!is_a($type, IdentifiableEntityInterface::class, true)) {
            throw new \InvalidArgumentException("$type does not implement " . IdentifiableEntityInterface::class);
        }

        parent::__construct($type);
    }

    /**
     * Set the (unique) entities.
     *
     * @param iterable<IdentifiableEntityInterface> $entities
     *
     * @phpstan-param iterable<TEntity> $entities
     */
    protected function setEntities(iterable $entities): void
    {
        $this->entities = Pipeline::with($entities)
            ->unique(fn(IdentifiableEntityInterface $entity) => $entity->getId())
            ->values()
            ->toArray();
    }

    /**
     * Add an entity to the set.
     *
     * @phpstan-param TEntity $entity
     */
    public function add(IdentifiableEntityInterface $entity): void
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
     * @param mixed|IdentifiableEntityInterface $find  Entity id or entity
     *
     * @phpstan-param mixed|TEntity $find
     */
    public function remove($find): void
    {
        $this->entities = Pipeline::with($this->entities)
            ->filter(fn(IdentifiableEntityInterface $entity) => !$entity->is($find))
            ->values()
            ->toArray();
    }
}
