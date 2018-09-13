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
     * @var int|\Closure
     */
    protected $totalCount;


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
     * Resolve total count if it's still a Closure.
     *
     * @return void
     * @throws \UnexpectedValueException if total count closure didn't return a positive integer
     */
    protected function resolveTotalCount(): void
    {
        if (is_int($this->totalCount) || !is_callable($this->totalCount)) {
            return;
        }

        $count = call_user_func($this->totalCount);

        if ((!is_int($count) && !ctype_digit($count)) || $count < 0) {
            $type = (is_object($count) ? get_class($count) . ' ' : '') . gettype($count);
            throw new \UnexpectedValueException("Failed to get total count: " .
                "Expected a positive integer, got " . (is_int($count) ? $count : $type));
        }

        $this->totalCount = (int)$count;
    }

    /**
     * Count all the entities (if set was limited)
     *
     * @return int
     * @throws \BadMethodCallException if total count isn't set
     * @throws \UnexpectedValueException if total count closure didn't return a positive integer
     */
    public function countTotal(): int
    {
        if (!isset($this->totalCount)) {
            throw new \BadMethodCallException("Total count is not set");
        }

        $this->resolveTotalCount();

        return $this->totalCount;
    }
}
