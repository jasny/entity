<?php

declare(strict_types=1);

namespace Jasny\EntityCollection;

use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use function Jasny\expect_type;

/**
 * An entity collection that works as an ordered set, so with unique items.
 * @see https://en.wikipedia.org/wiki/Set_(abstract_data_type)
 */
class EntitySet extends EntityCollection
{
    use Traits\SortTrait;

    /**
     * Class constructor
     *
     * @param string  $entityClass  Class name of entities in the collection
     * @throws \InvalidArgumentException if entity class is not an identifiable entity
     */
    public function __construct(string $entityClass)
    {
        if (!is_a($entityClass, IdentifiableEntity::class, true)) {
            $identifiable = IdentifiableEntity::class;
            throw new \InvalidArgumentException("$entityClass does not implement $identifiable");
        }

        parent::__construct($entityClass);
    }

    /**
     * Set the (unique) entities.
     *
     * @param Entity[]|iterable $entities
     * @return void
     */
    protected function setEntities(iterable $entities): void
    {
        $this->entities = [];
        $ids = [];

        foreach ($entities as $entity) {
            expect_type($entity, $this->getEntityClass());

            $id = $entity->getId();

            if ((isset($id) && in_array($id, $ids)) || in_array($entity, $this->entities, true)) {
                continue;
            }

            $this->entities[] = $entity;
            $ids[] = $id;
        }
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
     * Add an entity to the set
     *
     * @param Entity $entity
     * @return void
     */
    public function add(Entity $entity): void
    {
        expect_type($entity, $this->getEntityClass());

        if (!$this->findEntity($entity)->valid()) {
            $this->entities[] = $entity;
        }
    }

    /**
     * Remove an entity from the set
     *
     * @param mixed|Entity $entity  Entity id or entity
     * @return void
     */
    public function remove($entity): void
    {
        $index = $this->findEntity($entity)->key();

        if (isset($index)) {
            unset($this->entities[$index]);
            $this->entities = array_values($this->entities);
        }
    }
}
