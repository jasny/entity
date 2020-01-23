<?php

declare(strict_types=1);

namespace Jasny\EntityCollection;

use Jasny\Entity\Entity;
use Jasny\EntityCollection\Traits;
use function Jasny\expect_type;

/**
 * An entity collection that works as an ordered list.
 * @see https://en.wikipedia.org/wiki/List_(abstract_data_type)
 */
class EntityList extends EntityCollection
{
    use Traits\SortTrait;

    /**
     * Add an entity to the set
     *
     * @param Entity $entity
     * @return void
     */
    public function add(Entity $entity): void
    {
        expect_type($entity, $this->getEntityClass());

        $this->entities[] = $entity;
    }

    /**
     * Remove an entity from the set
     *
     * @param mixed|Entity $entity  Entity id or entity
     * @return void
     */
    public function remove($entity): void
    {
        $remove = $this->findEntity($entity);

        if (!$remove->valid()) {
            return;
        }

        foreach ($remove as $index => $cur) {
            unset($this->entities[$index]);
        }

        $this->entities = array_values($this->entities);
    }
}
