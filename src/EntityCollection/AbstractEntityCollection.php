<?php

namespace Jasny\EntityCollection;

use BadMethodCallException;
use InvalidArgumentException;
use OutOfBoundsException;
use Jasny\Entity\EntityInterface;
use Jasny\EntityCollection\EntityCollectionInterface;
use Jasny\EntityCollection\Traits\{
    ArrayAccessTrait,
    CountTrait,
    FilterTrait,
    GetSetTrait,
    IterableTrait,
    JsonSerializeTrait,
    MapReduceTrait,
    PropertyTrait,
    SearchTrait,
    SortTrait
};

/**
 * Base class for entity collections
 */
abstract class AbstractEntityCollection implements EntityCollectionInterface
{
    use ArrayAccessTrait;
    use CountTrait;
    use FilterTrait;
    use GetSetTrait;
    use IterableTrait;
    use JsonSerializeTrait;
    use MapReduceTrait;
    use PropertyTrait;
    use SearchTrait;
    use SortTrait;

    /**
     * The class name of the entities in this set
     * @var string
     */
    protected $entityClass;

    /**
     * @var Entity[]
     */
    protected $entities = [];

    /**
     * Total number of entities.
     * @var int|Closure
     */
    protected $totalCount;

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
     * Class constructor
     *
     * @codeCoverageIgnore
     * @param EntityInterface[]|iterable $entities  Array of entities
     * @param int|\Closure               $total     Total number of entities (if set is limited)
     */
    public function __construct(iterable $entities = [], $total = null)
    {
        $this->init($entities, $total);
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
