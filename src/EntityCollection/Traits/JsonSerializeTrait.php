<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\EntityInterface;

/**
 * Cast to json an instance of EntityCollection
 */
trait JsonSerializeTrait
{
    /**
     * Get the entities as array
     *
     * @return EntityInterface[]
     */
    abstract public function toArray();

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
