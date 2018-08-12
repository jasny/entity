<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\EntityInterface;

/**
 * Methods for creation of EntityCollection instances
 *
 * @property EntityInterface[] $entities
 * @property int $totalCount
 */
trait InitTrait
{
    /**
     * Set the entity class
     *
     * @throws BadMethodCallException When entity class is not set
     * @throws InvalidArgumentException When entity class does not represent an Entity or is not Identifiable
     */
    abstract protected function assertEntityClass();

    /**
     * Turn input into array of entities
     *
     * @param EntityInterface|mixed $entity
     */
    abstract protected function assertEntity($entity);

    /**
     * Factory method
     *
     * @param string                     $class     Class name
     * @param EntityInterface[]|iterable $entities  Array of entities
     * @param int|\Closure               $total     Total number of entities (if set is limited)
     * @return static
     * @throws ReflectionException
     * @throws DomainException                      If $class parameter does not correspond to entityClass property
     */
    public static function forClass(string $class, iterable $entities = [], $total = null): self
    {
        $refl = new \ReflectionClass(get_called_class());
        $entitySet = $refl->newInstanceWithoutConstructor();

        if (
            isset($entitySet->entityClass) &&
            $class !== $entitySet->entityClass &&
            !is_a($class, $entitySet->entityClass, true)
        ) {
            throw new \DomainException("{$refl->name} is only for {$entitySet->entityClass} entities, not $class");
        }

        $args = func_get_args();
        array_shift($args);

        $entitySet->entityClass = $class;
        $entitySet->__construct(...$args);

        return $entitySet;
    }

    /**
     * Init instance
     *
     * @param EntityInterface[]|iterable $entities  Array of entities
     * @param int|\Closure               $total     Total number of entities (if set is limited)
     */
    protected function init(iterable $entities = [], $total = null)
    {
        $this->assertEntityClass();

        $this->setEntities($entities);
        $this->totalCount = $total;
    }

    /**
     * Set the entities
     *
     * @param EntityInterface[]|iterable $entities
     * @return void
     */
    protected function setEntities(iterable $entities): void
    {
        $this->entities = [];

        foreach ($entities as $entity) {
            $this->assertEntity($entity);
            $this->entities[] = $entity;
        }
    }
}
