<?php

namespace Jasny\Entity\Traits;

use BadMethodCallException;
use InvalidArgumentException;
use ReflectionClass;
use function Jasny\array_only;
use function Jasny\object_set_properties;

/**
 * Entity lazy loading implementation
 *
 * @author  Arnold Daniels <arnold@jasny.net>
 * @license https://raw.github.com/jasny/entity/master/LICENSE MIT
 * @link    https://jasny.github.com/entity
 */
trait LazyLoadingTrait
{
    /**
     * Flag entity as ghost
     * @ignore
     * @var bool
     */
    private $i__ghost = false;


    /**
     * Check if the entity can hold and use undefined properties.
     *
     * @return bool
     */
    abstract public static function isDynamic(): bool;

    /**
     * Check if the entity is identifiable.
     *
     * @return bool
     */
    abstract public static function isIdentifiable(): bool;

    /**
     * Get the id property of the entity.
     *
     * @return string|null
     */
    abstract protected static function getIdProperty(): ?string;


    /**
     * Set the ghost state
     *
     * @param bool $state
     */
    final protected function markAsGhost(bool $state)
    {
        $this->i__ghost = $state;
    }

    /**
     * Check if the object is a ghost.
     *
     * @return bool
     */
    public function isGhost(): bool
    {
        return $this->i__ghost;
    }

    /**
     * Lazy load an entity, only the id is known.
     *
     * @param mixed $id
     * @return static
     * @throws BadMethodCallException if the entity is not identifiable.
     * @throws \ReflectionException
     */
    public static function lazyload($id)
    {
        $class = get_called_class();

        if (!static::isIdentifiable()) {
            throw new BadMethodCallException("$class entity is not identifiable");
        }

        $entity = (new ReflectionClass($class))->newInstanceWithoutConstructor();

        foreach (array_keys(get_class_vars($class)) as $prop) {
            unset($entity->$prop);
        }

        $idProp = static::getIdProperty();
        $entity->$idProp = $id;

        $entity->markAsGhost(true);

        return $entity;
    }

    /**
     * Reload with data from persisted storage.
     *
     * @param array $values
     * @return $this
     * @throws BadMethodCallException if entity is not identifiable
     * @throws InvalidArgumentException if data doesn't belong to entity
     */
    public function reload(array $values)
    {
        $class = get_class($this);

        if (!static::isIdentifiable()) {
            throw new BadMethodCallException("$class entity is not identifiable");
        }

        $data = $this->trigger("before:reload", $values);

        $id = $this->getId();
        $idProp = static::getIdProperty();

        if ($data[$idProp] !== $id) {
            throw new InvalidArgumentException("Id in reload data doesn't match entity id");
        }

        object_set_properties($this, $data, static::isDynamic());

        $this->trigger("after:reload");

        return $this;
    }
}
