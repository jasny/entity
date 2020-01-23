<?php

declare(strict_types=1);

namespace Jasny\Entity\Collection;

use Improved as i;
use Improved\IteratorPipeline\Pipeline;
use Jasny\Entity\EntityInterface;

/**
 * Base class for entity collections.
 *
 * @template TKey
 * @template TEntity of EntityInterface
 * @implements \IteratorAggregate<TKey,TEntity>
 */
abstract class AbstractCollection implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * The class name of the entities in this set.
     */
    private string $type;

    /**
     * @var EntityInterface[]
     * @phpstan-var array<TKey,TEntity>
     */
    protected array $entities = [];


    /**
     * Set the entities of the collection.
     *
     * @param iterable<EntityInterface> $entities
     *
     * @phpstan-param iterable<TEntity> $entities
     */
    abstract protected function setEntities(iterable $entities): void;


    /**
     * Class constructor.
     *
     * @param string $type  Class name of entities in the collection
     * @throws \InvalidArgumentException if entity class is not an identifiable entity
     *
     * @phpstan-param class-string<TEntity> $type
     */
    public function __construct(string $type = EntityInterface::class)
    {
        if (!is_a($type, EntityInterface::class, true)) {
            throw new \InvalidArgumentException("$type does not implement " . EntityInterface::class);
        }

        $this->type = $type;
    }


    /**
     * Get the class entities of this collection (must) have.
     */
    final public function getType(): string
    {
        return $this->type;
    }

    /**
     * Return a copy of the collection with a different class name for type checking.
     *
     * @param string $type
     * @return static
     *
     * @template TNew of EntityInterface
     * @phpstan-param class-string<TNew> $type
     * @phpstan-return static<TKey,TNew>
     */
    public function withType(string $type): self
    {
        if ($this->type === $type) {
            return $this;
        }

        if (!is_a($type, $this->type, true)) {
            throw new \InvalidArgumentException("$type does not implement {$this->type}");
        }

        Pipeline::with($this->entities)
            ->typeCheck($type, new \UnexpectedValueException("Not all entities are of type $type"))
            ->walk();

        $clone = clone $this;
        $clone->type = $type;

        return $clone;
    }

    /**
     * Create a new collection with given entities.
     *
     * @param iterable<EntityInterface> $entities
     * @return static
     *
     * @phpstan-param iterable<TEntity> $entities
     * @phpstan-return static
     */
    public function withEntities(iterable $entities): self
    {
        $entities = i\iterable_type_check($entities, $this->type);

        $collection = clone $this;
        $collection->setEntities($entities);

        return $collection;
    }


    /**
     * Get the iterator for looping through the set.
     *
     * @return \ArrayIterator<EntityInterface>
     *
     * @phpstan-return \ArrayIterator<TKey,TEntity>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->entities);
    }

    /**
     * Get the entities as array.
     *
     * @return EntityInterface[]
     *
     * @phpstan-return array<TKey,TEntity>
     */
    public function toArray(): array
    {
        return $this->entities;
    }

    /**
     * Count the number of entities.
     *
     * @return int
     */
    public function count()
    {
        return count($this->entities);
    }


    /**
     * Check if the entity exists in this set.
     *
     * @param mixed|EntityInterface $find  Entity id or entity
     * @return bool
     */
    public function contains($find): bool
    {
        return i\iterable_has_any($this->entities, fn(EntityInterface $entity) => $entity->is($find));
    }


    /**
     * Cast entity collection to data.
     *
     * @return EntityInterface[]
     *
     * @phpstan-return array<TEntity>
     */
    public function __serialize(): array
    {
        return $this->entities;
    }

    /**
     * Cast entity collection to data.
     *
     * @param array<EntityInterface|array> $entities
     *
     * @phpstan-param array<TEntity|array> $entities
     */
    public function __unserialize(array $entities): void
    {
        if (!isset($this->type)) {
            $this->__construct(); // Call the constructor explicitly to set the type if needed.
        }

        $setState = [$this->type, '__set_state'];

        $pipe = Pipeline::with($entities);
        if (is_callable($setState)) {
            $pipe->map(fn($entity) => is_array($entity) ? $setState($entity) : $entity);
        }
        $pipe->typeCheck($this->type);

        $this->setEntities($pipe);
    }

    /**
     * Create a new collection from serialized data.
     *
     * @param array<EntityInterface|array> $entities
     * @return static
     *
     * @phpstan-param array<TEntity|array> $entities
     * @phpstan-return static
     */
    public static function __set_state(array $entities)
    {
        $collection = new static();
        $collection->__unserialize($entities);

        return $collection;
    }

    /**
     * Prepare for JsonSerialize serialization
     *
     * @return array<EntityInterface>
     *
     * @phpstan-return array<TEntity>
     */
    public function jsonSerialize()
    {
        return $this->entities;
    }
}
