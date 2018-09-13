<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\EntityInterface;
use Jasny\EntityCollection\EntitySet;
use function Jasny\array_find;
use function Jasny\expect_type;

/**
 * Filter methods for EntityCollection
 */
trait FilterTrait
{
    /**
     * @var EntityInterface[]
     */
    protected $entities = [];

    /**
     * Get the class entities of this collection (must) have.
     *
     * @return string
     */
    abstract public function getEntityClass(): string;

    /**
     * Create a new collection.
     *
     * @param EntityInterface[]|iterable $entities  Array of entities
     * @param int|\Closure|null          $total     Total number of entities (if collection is limited)
     * @return static
     */
    abstract public function withEntities(iterable $entities, $total = null): self;


    /**
     * Get filter callback from property filter array
     *
     * @param array $filter
     * @param bool  $strict
     * @return \Closure
     */
    protected function getPropertyFilter(array $filter, bool $strict): \Closure
    {
        return function (EntityInterface $entity) use ($filter, $strict) {
            foreach ($filter as $key => $value) {
                $check = $entity->$key ?? null;

                if (($strict ? $value === $check : $value == $check) ||
                    (isset($check) && is_array($check) && in_array($value, $check, $strict))
                ) {
                    return true;
                }
            }

            return count($filter) === 0;
        };
    }


    /**
     * Create a new EntitySet.
     * @codeCoverageIgnore
     *
     * @return EntitySet
     */
    protected function createEntitySet(): EntitySet
    {
        return new EntitySet($this->getEntityClass());
    }


    /**
     * Return a unique set of entities (based on id).
     *
     * @return EntitySet
     */
    public function unique(): EntitySet
    {
        return $this->createEntitySet()->withEntities($this->entities);
    }

    /**
     * Filter the elements using a callback or by property
     *
     * @param array|\Closure $filter
     * @param int|bool       $flag    Strict if filter is an array or ARRAY_FILTER_* constant for a callable
     * @return static
     */
    public function filter($filter, $flag = 0): self
    {
        expect_type($filter, ['array', \Closure::class]);

        if (is_array($filter)) {
            $filter = $this->getPropertyFilter($filter, (bool)$flag);
            $flag = 0;
        }

        $filtered = array_filter($this->entities, $filter, $flag);

        return $this->withEntities($filtered);
    }

    /**
     * Find first entity that passed a filter.
     *
     * @param array|\Closure $filter
     * @param int|bool       $flag    Strict if filter is an array or ARRAY_FILTER_* constant for a callable
     * @return EntityInterface|null
     */
    public function find($filter, $flag = 0): ?EntityInterface
    {
        expect_type($filter, ['array', \Closure::class]);

        if (is_array($filter)) {
            $filter = $this->getPropertyFilter($filter, (bool)$flag);
            $flag = 0;
        }

        $found = array_find($this->entities, $filter, $flag);

        return $found !== false ? $found : null;
    }
}
