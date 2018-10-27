<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\Entity;

/**
 * Count entities methods for EntityCollection
 */
trait CountTrait
{
    /**
     * @var Entity[]
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
