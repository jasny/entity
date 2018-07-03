<?php

namespace Jasny\Entity\Traits;

use BadMethodCallException;

/**
 * Entity identifiable implementation
 *
 * @author  Arnold Daniels <arnold@jasny.net>
 * @license https://raw.github.com/jasny/entity/master/LICENSE MIT
 * @link    https://jasny.github.com/entity
 */
trait IdentifiableTrait
{
    /**
     * Check if the entity is identifiable.
     *
     * @return bool
     */
    public static function isIdentifiable(): bool
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
     * @throws BadMethodCallException if the entity is not identifiable.
     */
    public function getId()
    {
        $prop = static::getIdProperty();

        if (!isset($prop)) {
            throw new BadMethodCallException(get_called_class() . " entity is not identifiable");
        }

        return $this->$prop;
    }
}
