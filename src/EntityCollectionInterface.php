<?php

namespace Jasny;

use Jasny\EntityInterface;
use IteratorAggregate;
use ArrayAccess;
use Countable;
use JsonSerializable;

/**
 * A collection (array) of entities
 */
interface EntityCollectionInterface extends IteratorAggregate, ArrayAccess, Countable, JsonSerializable
{
    /**
     * Class constructor
     *
     * @param EntityInterface[]|    iterable $entities  Array of entities
     * @param int|\Closure               $total     Total number of entities (if set is limited)
     */
    public function __construct(iterable $entities = [], $total = null);

    /**
     * Get the class entities of this set (must) have
     *
     * @return string
     */
    public function getEntityClass(): string;

    /**
     * Get the entities as array
     *
     * @return EntityInterface[]
     */
    public function toArray();

    /**
     * Count all the entities (if set was limited)
     *
     * @return int
     */
    public function countTotal();


    /**
     * Check if the entity exists in this set
     *
     * @param mixed|EntityInterface $entity   Entity id or Entity
     * @return boolean
     */
    public function contains($entity);

    /**
     * Get an entity from the set by id
     *
     * @param mixed|EntityInterface $entity   Entity id or Entity
     * @return EntityInterface|null
     */
    public function get($entity): ?EntityInterface;

    /**
     * Add an entity to the set
     *
     * @param EntityInterface $entity
     * @return void
     */
    public function add(EntityInterface $entity): void;

    /**
     * Remove an entity from the set
     *
     * @param mixed|EntityInterface $entity
     * @return void
     */
    public function remove($entity): void;


    /**
     * Return a unique set of entities.
     *
     * @return EntityCollectionInterface
     */
    public function unique();

    /**
     * Filter the elements
     *
     * @param array|Closure $filter
     * @param bool          $strict  Strict comparison when filtering on properties
     * @return EntityCollectionInterface
     */
    public function filter($filter, $strict = false);


    /**
     * Sort the entities as string or on a property.
     *
     * @param string $property
     * @param int    $sortFlags
     * @return $this
     */
    public function sort(string $property = null, int $sortFlags = SORT_REGULAR);

    /**
     * Sort the entities as string or on a property.
     *
     * @return $this
     */
    public function reverse();


    /**
     * Apply callback to every entity and return result
     *
     * @param callable $callback
     * @return array
     */
    public function map(callable $callback): array;

    /**
     * Map items to entity via callback
     *
     * @param iterable $items     One item per entity, mapped by id (not index)
     * @param callable $callback
     * @return array
     */
    public function mapItems(iterable $items, callable $callback): array;

    /**
     * Reduce items into a single value via callback
     *
     * @param callable $callback
     * @param mixed    $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null);

    /**
     * Get property of all entities.
     *
     * @param string $property
     * @param bool   $skipNotSet
     * @return iterable
     */
    public function getAll(string $property, bool $skipNotSet = true): iterable;

    /**
     * Get property of all entities as associative array with id as key.
     *
     * @param string $property
     * @param bool   $skipNotSet
     * @return iterable
     */
    public function getAllById(string $property, bool $skipNotSet = true): iterable;

    /**
     * Get unique values for property of all entities.
     *
     * @param string $property
     * @param bool   $flatten   Flatten array
     * @return iterable
     */
    public function getUnique(string $property, bool $flatten = false): iterable;
}
