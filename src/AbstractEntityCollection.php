<?php

declare(strict_types=1);

namespace Jasny\EntityCollection;

use Jasny\Entity\EntityInterface;
use Jasny\EntityCollection\Traits;
use function Jasny\expect_type;

/**
 * Base class for entity collections.
 */
abstract class AbstractEntityCollection implements EntityCollectionInterface
{
    use Traits\CountTrait;
    use Traits\FilterTrait;
    use Traits\GetTrait;
    use Traits\IterableTrait;
    use Traits\JsonSerializeTrait;
    use Traits\MapReduceTrait;
    use Traits\PropertyTrait;

    /**
     * The class name of the entities in this set
     * @var string
     */
    private $entityClass;

    /**
     * @var EntityInterface[]
     */
    protected $entities = [];

    /**
     * Total number of entities (if collection is limited).
     * @var int|Closure
     */
    protected $totalCount;


    /**
     * Class constructor
     *
     * @param string $entityClass  Class name of entities in the collection
     * @throws \InvalidArgumentException if entity class doesn't implement EntityInterface
     */
    public function __construct(string $entityClass)
    {
        if (!is_a($entityClass, EntityInterface::class, true)) {
            throw new \InvalidArgumentException("$entityClass does not implement " . EntityInterface::class);
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
     * @param EntityInterface[]|iterable $entities  Array of entities
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
     * @param EntityInterface[]|iterable $entities  Array of entities
     * @param int|\Closure|null          $total     Total number of entities (if collection is limited)
     * @return static
     */
    public function withEntities(iterable $entities, $total = null): self
    {
        expect_type($total, ['int', \Closure::class, 'null']);

        $collection = clone $this;
        $collection->setEntities($entities);
        $collection->totalCount = $total;

        return $collection;
    }
}
