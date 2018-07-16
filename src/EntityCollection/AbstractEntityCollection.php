<?php

namespace Jasny\EntityCollection;

use Jasny\Entity;
use Jasny\EntityInterface;
use Jasny\EntityCollectionInterface;
use BadMethodCallException;
use InvalidArgumentException;
use DomainException;
use OutOfBoundsException;
use ReflectionClass;
use ArrayIterator;
use Closure;
use Generator;

/**
 * Base class for entity collections
 */
class AbstractEntityCollection implements EntityCollectionInterface
{
    /**
     * The class name of the entities in this set
     * @var string
     */
    protected $entityClass;
    
    /**
     * @var Entity[]
     */
    protected $entities;

    /**
     * Total number of entities.
     * @var int|Closure
     */
    protected $totalCount;
    
    
    /**
     * Class constructor
     *
     * @param EntityInterface[]|iterable $entities  Array of entities
     * @param int|\Closure               $total     Total number of entities (if set is limited)
     */
    public function __construct(iterable $entities = [], $total = null)
    {
        $this->assertEntityClass();

        $this->setEntities($entities);
        $this->totalCount = $total;
    }

    /**
     * Factory method
     * 
     * @param string                     $class     Class name
     * @param EntityInterface[]|iterable $entities  Array of entities
     * @param int|\Closure               $total     Total number of entities (if set is limited)
     * @return static
     * @throws \ReflectionException
     */
    public static function forClass(string $class, iterable $entities = [], $total = null): self
    {
        $refl = new ReflectionClass(get_called_class());
        
        $entitySet = $refl->newInstanceWithoutConstructor();

        if (
            isset($entitySet->entityClass) &&
            $class !== $entitySet->entityClass &&
            !is_a($class, $entitySet->entityClass, true)
        ) {
            throw new DomainException("{$refl->name} is only for {$entitySet->entityClass} entities, not $class");
        }

        $args = func_get_args();
        array_shift($args);
        
        $entitySet->__construct(...$args);
        
        return $entitySet;
    }


    /**
     * Set the entity class
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

        if (!$class::isIdentifiable()) {
            throw new InvalidArgumentException("$class is not an identifiable, can't create a set");
        }
    }

    /**
     * Turn input into array of entities
     * 
     * @param EntityInterface|mixed $entity
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
     * @param boolean $add     Indexed is used for adding an element
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
    
    /**
     * Get the class entities of this set (must) have
     * 
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * Set the entities
     * 
     * @param EntityInterface[]|iterable $entities
     * @return void
     */
    protected function setEntities(iterable $entities): void
    {
        foreach ($entities as $entity) {
            $this->assertEntity($entity);
            $this->entities[] = $entity;
        }
    }

    
    /**
     * Get the iterator for looping through the set
     * 
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->entities);
    }
    
    /**
     * Get the entities as array
     * 
     * @return EntityInterface[]
     */
    public function toArray()
    {
        return $this->entities;
    }

    /**
     * Count the number of entities
     * 
     * @return int
     */
    public function count()
    {
        return count($this->entities);
    }

    /**
     * Count all the entities (if set was limited)
     * 
     * @return int
     */
    public function countTotal()
    {
        if ($this->totalCount instanceof Closure) {
            $this->totalCount = call_user_func($this->totalCount);
        }
        
        return $this->totalCount ?? $this->count();
    }


    /**
     * Find an entity by id or reference
     *
     * @param mixed|EntityInterface $entity
     * @return Generator
     */
    protected function findEntity($entity)
    {
        return $entity instanceof EntityInterface ? $this->findEntityByRef() : $this->findEntityById();
    }

    /**
     * Find an entity by reference
     *
     * @param EntityInterface $entity
     * @return Generator
     */
    protected function findEntityByRef(EntityInterface $entity)
    {
        $hasId = $entity::isIdentifiable() && $entity->getId() !== null;

        foreach ($this->entities as $index => $cur) {
            if ($cur === $entity || ($hasId && $cur->getId() === $entity->getId())) {
                yield $index => $cur;
            }
        }
    }

    /**
     * Find an entity by id
     *
     * @param mixed $id
     * @return Generator
     */
    protected function findEntityById($id)
    {
        foreach ($this->entities as $index => $cur) {
            if ($cur->getId() === $id) {
                yield $index => $cur;
            }
        }
    }


    /**
     * Check if the entity exists in this set
     * 
     * @param mixed|EntityInterface $entity
     * @return boolean
     */
    public function contains($entity)
    {
        return $this->get($entity) !== null;
    }

    /**
     * Get an entity from the set by id
     * 
     * @param mixed|EntityInterface $entity   Entity id or Entity
     * @return EntityInterface|null
     */
    public function get($entity): ?EntityInterface
    {
        return $this->findEntity($entity)->current();
    }
    
    /**
     * Add an entity to the set
     * 
     * @param EntityInterface $entity
     * @return void
     */
    public function add(EntityInterface $entity): void
    {
        $this->offsetSet(null, $entity);
    }
    
    /**
     * Remove an entity from the set
     * 
     * @param mixed|EntityInterface $entity
     * @return void
     */
    public function remove($entity): void
    {
        foreach ($this->findEntity($entity) as $index => $cur) {
            unset($this->entities[$index]);
        }
    }
    


    /**
     * Prepare for JsonSerialize serialization
     * 
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
