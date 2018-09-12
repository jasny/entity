<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\EntityInterface;

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
    abstract public function toArray(): array;

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
