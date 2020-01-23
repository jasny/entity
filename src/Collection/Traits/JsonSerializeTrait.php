<?php

namespace Jasny\EntityCollection\Traits;

use Jasny\Entity\Entity;

/**
 * Cast to json an instance of EntityCollection
 */
trait JsonSerializeTrait
{
    /**
     * Get the entities as array
     *
     * @return Entity[]
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
