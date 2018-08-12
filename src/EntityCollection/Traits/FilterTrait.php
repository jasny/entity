<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\EntityInterface;
use Closure;
use function Jasny\expect_type;

/**
 * Filter methods for EntityCollection
 *
 * @property EntityInterface[] $entities
 */
trait FilterTrait
{
    /**
     * Return a unique set of entities (based on id)
     *
     * @return static
     */
    public function unique()
    {
        $index = [];

        return $this->filter(function(EntityInterface $entity) use (&$index) {
            $id = $entity->getId();

            $double = isset($index[$id]);
            $index[$id] = true;

            return !$double;
        });
    }

    /**
     * Filter the elements using a callback or by property
     *
     * @param array|Closure $filter
     * @param bool          $strict  Strict comparison when filtering on properties
     * @return static
     */
    public function filter($filter, $strict = false)
    {
        expect_type($filter, ['array', Closure::class]);

        if (is_array($filter)) {
            $filter = function(EntityInterface $entity) use ($filter, $strict) {
                foreach ($filter as $key => $value) {
                    $check = $entity->$key ?? null;

                    if (
                        ($strict ? $value === $check : $value == $check) ||
                        (isset($check) && is_array($check) && in_array($value, $check, $strict))
                    ) {
                        return true;
                    }
                }

                return !count($filter);
            };
        }

        return $this->applyFilter($filter);
    }

    /**
     * Apply filter to entities
     *
     * @param callable $filter
     * @return static
     */
    protected function applyFilter($filter)
    {
        $filtered = array_filter($this->entities, $filter);

        $filteredSet = clone $this;
        $filteredSet->entities = array_values($filtered);

        return $filteredSet;
    }
}
