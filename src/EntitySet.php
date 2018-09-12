<?php

declare(strict_types=1);

namespace Jasny\EntityCollection;

use Jasny\Entity\EntityInterface;
use function Jasny\expect_type;

/**
 * An entity collection that works as an ordered set, so with unique items.
 * @see https://en.wikipedia.org/wiki/Set_(abstract_data_type)
 */
class EntitySet extends AbstractEntityCollection implements EntitySetInterface
{
    use Traits\SortTrait;

    /**
     * Entities mapped by id
     * @var EntityInterface[]
     */
    protected $map = [];


    /**
     * Class constructor
     *
     * @param string  $entityClass  Class name of entities in the collection
     * @throws \InvalidArgumentException if entity class is not EntityInterface or not Identifiable
     */
    public function __construct(string $entityClass)
    {
        if (!is_a($entityClass, EntityInterface::class, true)) {
            throw new \InvalidArgumentException("$entityClass does not implement " . EntityInterface::class);
        }

        if (!is_callable([$entityClass, 'hasIdProperty']) || !$entityClass::hasIdProperty()) {
            throw new \InvalidArgumentException("$entityClass is not uniquely identifiable; can't create unique set");
        }

        parent::__construct($entityClass);
    }

    /**
     * Set the entities
     *
     * @param EntityInterface[]|iterable $entities
     * @return void
     */
    protected function setEntities(iterable $entities): void
    {
        $this->entities = [];
        $this->map = [];

        foreach ($entities as $entity) {
            expect_type($entity, $this->getEntityClass());

            $id = $entity->getId();

            if (isset($this->map[$id])) {
                continue;
            }

            $this->entities[] = $entity;
            $this->map[$id] = $entity;
        }
    }

    /**
     * Check if the entity exists in this set.
     *
     * @param mixed|EntityInterface $entity
     * @return bool
     */
    public function contains($entity): bool
    {
        $id = $entity instanceof EntityInterface ? $entity->getId() : $entity;

        return isset($this->map[$id]);
    }

    /**
     * Return a unique set of entities.
     *
     * @return $this
     */
    public function unique(): self
    {
        return $this;
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
        $id = $entity instanceof EntityInterface ? $entity->getId() : $entity;

        return isset($id) && isset($this->map[$id]) ? $this->map[$id] : null;
    }

    /**
     * Add an entity to the set.
     * If the entity already is in the set, it's replaced.
     *
     * @param EntityInterface $entity
     * @return void
     */
    public function add(EntityInterface $entity): void
    {
        expect_type($entity, $this->getEntityClass());

        $id = $entity instanceof EntityInterface ? $entity->getId() : $entity;

        if (isset($this->map[$id])) {
            $index = $this->findEntityById($id)->key();

            $this->map[$id] = $entity;
            $this->entities[$index] = $entity;
        } else {
            $this->entities[] = $entity;
            $this->map[$entity->getId()] = $entity;
        }
    }

    /**
     * Remove an entity from the set
     *
     * @param mixed|EntityInterface $entity   Entity id or Entity
     * @return void
     */
    public function remove($entity): void
    {
        $id = $entity instanceof EntityInterface ? $entity->getId() : $entity;

        if (isset($this->map[$id])) {
            $index = $this->findEntityById($id)->key();
            unset($this->map[$id], $this->entities[$index]);

            $this->entities = array_values($this->entities);
        }
    }
}
