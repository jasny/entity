<?php

namespace Jasny\EntityCollection;

use Jasny\EntityCollectionInterface;
use Jasny\EntityCollection\Traits\{
    ArrayAccessTrait,
    AssertTrait,
    CountTrait,
    FilterTrait,
    GetSetTrait,
    InitTrait,
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
class AbstractEntityCollection implements EntityCollectionInterface
{
    use ArrayAccessTrait;
    use AssertTrait;
    use CountTrait;
    use FilterTrait;
    use GetSetTrait;
    use InitTrait;
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
}
