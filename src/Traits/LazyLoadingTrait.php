<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use BadMethodCallException;
use ReflectionClass;
use ReflectionException;

/**
 * Entity lazy loading implementation
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
     * Get entity id.
     *
     * @return mixed
     * @throws \BadMethodCallException if the entity is not identifiable.
     */
    abstract public function getId();

    /**
     * Get the id property of the entity.
     *
     * @return string|null
     */
    abstract protected static function getIdProperty(): ?string;


    /**
     * Set the ghost state.
     *
     * @param bool $state
     * @return void
     */
    final protected function markAsGhost(bool $state): void
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
     * @throws ReflectionException
     */
    public static function fromId($id)
    {
        $class = get_called_class();

        /** @var static $entity */
        $entity = (new ReflectionClass($class))->newInstanceWithoutConstructor();

        foreach (array_keys(get_class_vars($class)) as $prop) {
            unset($entity->$prop);
        }

        $idProp = static::getIdProperty();
        $entity->$idProp = $id;

        $entity->markAsGhost(true);

        return $entity;
    }
}
