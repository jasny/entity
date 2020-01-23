<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use function Jasny\is_associative_array;

/**
 * Check if two entities are the same.
 *
 * @implements Entity
 */
trait CompareTrait
{
    /**
     * Check if this entity is the same as another entity
     *
     * @param Entity $entity
     * @return bool
     */
    protected function isSameAsEntity(Entity $entity): bool
    {
        return
            $this === $entity ||
            (
                $this instanceof IdentifiableEntity &&
                $entity instanceof IdentifiableEntity &&
                get_class($this) === get_class($entity) &&
                $this->getId() === $entity->getId()
            );
    }

    /**
     * Check if entity matches given values.
     *
     * @param array $filter
     * @return bool
     */
    protected function matchesFilter(array $filter): bool
    {
        foreach ($filter as $key => $comp) {
            $value = ($this->$key ?? null);
            $match = is_scalar($value) && is_scalar($comp) ? (string)$value === (string)$comp : $value === $comp;

            if (!$match) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if entity matches given id.
     *
     * @param mixed $filter
     * @return bool
     */
    protected function matchedId($filter): bool
    {
        if (!$this instanceof IdentifiableEntity) {
            return false;
        }

        $id = $this->getId();

        return is_scalar($id) && is_scalar($filter) ? (string)$id === (string)$filter : $id === $filter;
    }

    /**
     * Check if entity is the same as the provided entity or matches id or filter.
     * For a filter, scalars are compared as string.
     *
     * @param Entity|array|mixed $filter
     * @return bool
     */
    public function is($filter): bool
    {
        if ($filter instanceof Entity) {
            return $this->isSameAsEntity($filter);
        }

        if (is_associative_array($filter)) {
            return $this->matchesFilter($filter);
        }

        return $this->matchedId($filter);
    }
}
