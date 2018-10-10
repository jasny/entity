<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\EntityInterface;

/**
 * Count entities methods for EntityCollection
 */
trait CountTrait
{
    /**
     * @var EntityInterface[]
     */
    protected $entities = [];

    /**
     * Count the number of entities
     *
     * @return int
     */
    public function count()
    {
        return count($this->entities);
    }
}
