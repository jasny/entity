<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\EntityCollection\EntitySet;
use Jasny\EntityCollectionInterface;
use Jasny\EntityInterface;
use Closure;
use BadMethodCallException;
use InvalidArgumentException;
use OutOfBoundsException;
use function Jasny\expect_type;

/**
 * Assertion methods for EntityCollection
 *
 * @property EntityInterface[] $entities
 * @property string $entityClass
 */
trait AssertTrait
{
    /**
     * Set the entity class
     *
     * @throws BadMethodCallException When entity class is not set
     * @throws InvalidArgumentException When entity class does not represent an Entity or is not Identifiable
     */
    protected function assertEntityClass()
    {
        $class = $this->entityClass;

        if (empty($class)) {
            throw new BadMethodCallException("Entity class not set");
        }

        if (!is_a($class, EntityInterface::class, true)) {
            throw new InvalidArgumentException("$class is not an Entity");
        }
    }

    /**
     * Turn input into array of entities
     *
     * @param EntityInterface|mixed $entity
     * @throws InvalidArgumentException If $entity is not an instance of Entity or of $this->entityClass
     */
    protected function assertEntity($entity)
    {
        if (!$entity instanceof EntityInterface) {
            $type = (is_object($entity) ? get_class($entity) . ' ' : '') . gettype($entity);
            throw new InvalidArgumentException("$type is not an Entity");
        }

        if (!is_a($entity, $this->entityClass)) {
            throw new InvalidArgumentException(get_class($entity) . " is not a {$this->entityClass} entity");
        }
    }

    /**
     * Check if index is an integer and not out of bounds.
     *
     * @param int     $index
     * @param boolean $add              Index is used for adding an element
     * @throws InvalidArgumentException If index is not numeric
     * @throws OutOfBoundsException     If index is out of bounds
     */
    protected function assertIndex($index, $add = false)
    {
        if (!is_int($index)) {
            throw new InvalidArgumentException("Only numeric keys are allowed");
        }

        if ($index < 0 || $index > count($this->entities) - ($add ? 0 : 1)) {
            throw new OutOfBoundsException("Index '$index' is out of bounds");
        }
    }
}
