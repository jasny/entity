<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\IdentifiableEntityInterface;

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
    abstract static public function getIdProperty(): ?string;

    /**
     * Trigger before an event.
     *
     * @param string $event
     * @param mixed  $payload
     * @return mixed|void
     */
    abstract public function trigger(string $event, $payload = null);


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
     * @throws \BadMethodCallException if the entity is not identifiable.
     * @throws \ReflectionException
     */
    public static function lazyload($id)
    {
        $class = get_called_class();

        /** @var static $entity */
        $entity = (new \ReflectionClass($class))->newInstanceWithoutConstructor();

        foreach (array_keys(get_class_vars($class)) as $prop) {
            unset($entity->$prop);
        }

        $idProp = static::getIdProperty();
        $entity->$idProp = $id;

        $entity->markAsGhost(true);

        return $entity;
    }
}
