<?php

namespace Jasny\Entity\Traits;

use LogicException;

/**
 * Entity identifiable implementation
 */
trait IdentifyTrait
{
    /**
     * Get the id property of the entity.
     *
     * @return string|null
     * @throws \LogicException if id property is unknown
     */
    protected static function getIdProperty(): ?string
    {
        if (!property_exists(get_called_class(), 'id')) {
            throw new LogicException("Unknown id property for " . get_called_class() . " entity");
        }

        return 'id';
    }

    /**
     * Get entity id.
     *
     * @return mixed
     */
    public function getId()
    {
        $prop = static::getIdProperty();

        return $this->$prop;
    }
}
