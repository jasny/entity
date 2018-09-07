<?php

namespace Jasny\EntityCollection\Traits;

use Closure;
use Jasny\Entity\EntityInterface;

/**
 * Count entities methods for EntityCollection
 *
 * @property EntityInterface[] $entities
 * @property int $totalCount
 */
trait CountTrait
{
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
}
