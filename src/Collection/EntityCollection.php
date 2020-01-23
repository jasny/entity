<?php

declare(strict_types=1);

namespace Jasny\EntityCollection;

use Jasny\Entity\Entity;
use Jasny\EntityCollection\Traits;
use function Jasny\expect_type;

/**
 * Base class for entity collections.
 */
abstract class EntityCollection implements \IteratorAggregate, \Countable, \JsonSerializable
{
    use Traits\CountTrait;
    use Traits\FilterTrait;
    use Traits\GetTrait;
    use Traits\TraversableTrait;
    use Traits\JsonSerializeTrait;
    use Traits\MapReduceTrait;
    use Traits\PropertyTrait;

    /**
     * The class name of the entities in this set
     * @var string
     */
    private $entityClass;

    /**
     * @var Entity[]
     */
    protected $entities = [];


    /**
     * Class constructor
     *
     * @param string $entityClass  Class name of entities in the collection
     * @throws \InvalidArgumentException if entity class doesn't implement Entity
     */
    public function __construct(string $entityClass)
    {
        if (!is_a($entityClass, Entity::class, true)) {
            throw new \InvalidArgumentException("$entityClass does not implement " . Entity::class);
        }

        $this->entityClass = $entityClass;
    }

    /**
     * Get the class entities of this collection (must) have.
     *
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }


    /**
     * Set the entities.
     *
     * @param Entity[]|iterable $entities  Array of entities
     * @return void
     */
    protected function setEntities(iterable $entities): void
    {
        $this->entities = [];
        $entityClass = $this->getEntityClass();

        foreach ($entities as $index => $entity) {
            expect_type($entity, $entityClass, "Expected {$entityClass} for item {$index}, %s given");
            $this->entities[] = $entity;
        }
    }

    /**
     * Create a new collection.
     *
     * @param Entity[]|iterable $entities  Array of entities
     * @return static
     */
    public function withEntities(iterable $entities): self
    {
        $collection = clone $this;
        $collection->setEntities($entities);

        return $collection;
    }
}
