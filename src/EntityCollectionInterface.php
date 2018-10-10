<?php

declare(strict_types=1);

namespace Jasny\EntityCollection;

use Jasny\Entity\EntityInterface;

/**
 * A collection (array) of entities
 */
interface EntityCollectionInterface extends \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * Create new collection.
     * Prototype interface.
     *
     * @param EntityInterface[]|iterable $entities  Array of entities
     * @return static
     */
    public function withEntities(iterable $entities);

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
    public function toArray(): array;


    /**
     * Check if the entity exists in this set
     *
     * @param mixed|EntityInterface $entity   Entity id or Entity
     * @return bool
     */
    public function contains($entity): bool;

    /**
     * Get an entity from the collection by id
     *
     * @param mixed|EntityInterface $entity   Entity id or Entity
     * @return EntityInterface
     * @throws \OutOfBoundsException if entity is not in collection
     */
    public function get($entity): ?EntityInterface;


    /**
     * Return a unique set of entities.
     *
     * @return EntitySet
     */
    public function unique();

    /**
     * Filter the entities.
     *
     * @param array|callable $filter
     * @param int|bool       $flag    Strict if filter is an array or FILTER_* contant for a callable
     * @return self
     */
    public function filter($filter, $flag = 0);

    /**
     * Find first entity that passed a filter.
     *
     * @param array|\Closure $filter
     * @param int|bool       $flag    Strict if filter is an array or FILTER_* contant for a callable
     * @return EntityInterface|null
     */
    public function find($filter, $flag = 0): ?EntityInterface;


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
     * @param array    $items     One item per entity, mapped by id (not index)
     * @param callable $callback
     * @return array
     */
    public function mapItems(array $items, callable $callback): array;

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
     * @return array
     */
    public function getAll(string $property, bool $skipNotSet = true): array;

    /**
     * Get property of all entities as associative array with id as key.
     *
     * @param string $property
     * @param bool   $skipNotSet
     * @return array
     */
    public function getAllById(string $property, bool $skipNotSet = true): array;

    /**
     * Get unique values for property of all entities.
     *
     * @param string $property
     * @param bool   $flatten   Flatten array
     * @return array
     */
    public function getUnique(string $property, bool $flatten = false): array;

    /**
     * Get sum of property values of all entities
     *
     * @param string $property
     * @return int|float
     */
    public function getSumOf(string $property);
}
