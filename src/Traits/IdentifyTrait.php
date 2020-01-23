<?php

namespace Jasny\Entity\Traits;

/**
 * EntityInterface identifiable implementation.
 */
trait IdentifyTrait
{
    /**
     * Get entity id.
     *
     * @return mixed
     */
    public function getId()
    {
        if (!property_exists($this, 'id')) {
            throw new \LogicException("Unknown id property for " . get_called_class() . " entity");
        }

        /** @noinspection PhpUndefinedFieldInspection */
        return $this->id;
    }
}
