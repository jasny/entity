<?php

namespace Jasny\Entity\Traits;

use Jasny\Entity\Exception\NotIdentifiableException;

/**
 * Entity identifiable implementation
 *
 * @author  Arnold Daniels <arnold@jasny.net>
 * @license https://raw.github.com/jasny/entity/master/LICENSE MIT
 * @link    https://jasny.github.com/entity
 */
trait IdentifyTrait
{
    /**
     * Check if the entity has an id property.
     *
     * @return bool
     */
    public static function hasIdProperty(): bool
    {
        return static::getIdProperty() !== null;
    }

    /**
     * Get the id property of the entity
     *
     * @return string|null
     */
    protected static function getIdProperty(): ?string
    {
        return property_exists(get_called_class(), 'id') ? 'id' : null;
    }

    /**
     * Get entity id.
     *
     * @return mixed
     * @throws NotIdentifiableException if the entity is not identifiable.
     */
    public function getId()
    {
        $prop = static::getIdProperty();

        if (!isset($prop)) {
            throw new NotIdentifiableException(get_called_class() . " entity is not identifiable");
        }

        return $this->$prop;
    }
}
