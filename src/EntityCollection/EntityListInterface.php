<?php

declare(strict_types=1);

namespace Jasny\EntityCollection;

use Jasny\Entity\EntityInterface;

/**
 * An entity collection that works as an ordered list.
 * @see https://en.wikipedia.org/wiki/List_(abstract_data_type)
 */
interface EntityListInterface extends EntityCollectionInterface
{
    /**
     * Add an entity to the collection
     *
     * @param EntityInterface $entity
     * @return void
     */
    public function add(EntityInterface $entity): void;

    /**
     * Remove an entity from the collection
     *
     * @param mixed|EntityInterface $entity
     * @return void
     */
    public function remove($entity): void;


    /**
     * Sort the entities as string or on a property.
     *
     * @param string $property
     * @param int    $sortFlags
     * @return void
     */
    public function sort(string $property = null, int $sortFlags = SORT_REGULAR): void;

    /**
     * Reverse the order of entities.
     *
     * @return void
     */
    public function reverse(): void;
}
