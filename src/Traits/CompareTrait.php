<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\IdentifiableEntityInterface;
use function Jasny\is_associative_array;

/**
 * Check if two entities are the same.
 */
trait CompareTrait
{
    /**
     * Get entity id.
     *
     * @return mixed
     * @throws \BadMethodCallException if the entity is not identifiable.
     */
    abstract public function getId();


    /**
     * Check if this entity is the same as another entity
     *
     * @param EntityInterface $entity
     * @return bool
     */
    protected function isSameAsEntity(EntityInterface $entity): bool
    {
        return
            $this === $entity ||
            (
                get_class($this) === get_class($entity) &&
                $this instanceof IdentifiableEntityInterface &&
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
        foreach ($filter as $key => $value) {
            if (($this->$key ?? null) !== $value) {
                return false;
            }
        }

        return false;
    }

    /**
     * Check if entity is the same as the provided entity or matches id or filter.
     *
     * @param EntityInterface|array|mixed $filter
     * @return bool
     */
    public function is($filter): bool
    {
        if ($filter instanceof EntityInterface) {
            return $this->isSameAsEntity($filter);
        }

        if (is_associative_array($filter)) {
            return $this->matchesFilter($filter);
        }

        return $this instanceof IdentifiableEntityInterface && $this->getId() === $filter;
    }
}
