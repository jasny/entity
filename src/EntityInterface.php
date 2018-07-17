<?php

namespace Jasny;

use JsonSerializable;
use Jasny\Meta\MetaCastInterface;
use Jasny\ValidationInterface;

/**
 * An entity is an object with a (persistent) data representation.
 */
interface EntityInterface extends JsonSerializable
{
    /**
     * Check if the entity can hold and use undefined properties.
     *
     * @return bool
     */
    public static function isDynamic(): bool;

    /**
     * Set a value or multiple values.
     *
     * <code>
     *   $entity->set('foo', 22);
     *   $entity->set('bar', null);
     *   $entity->set(['qux' => 100, 'clr' => 'red']);
     * </code>
     *
     * @param string|array|object $key
     * @param mixed               $value
     * @return $this
     */
    public function set($key, $value = null);

    /**
     * Cast the entity to an associative array.
     *
     * @return array
     */
    public function toAssoc(): array;


    /**
     * Check if the entity has an id property
     *
     * @return bool
     */
    public static function hasIdProperty(): bool;

    /**
     * Get entity id.
     *
     * @return mixed
     * @throws BadMethodCallException if the entity is not identifiable.
     */
    public function getId();


    /**
     * Check if the object is a ghost.
     *
     * @return bool
     */
    public function isGhost(): bool;

    /**
     * Lazy load an entity, only the id is known
     *
     * @param mixed             $id
     * @return static
     * @throws BadMethodCallException if the entity is not identifiable.
     */
    public static function lazyload($id);

    /**
     * Reload with data from persisted storage.
     *
     * @param array $data
     * @return $this
     */
    public function reload(array $data);


    /**
     * Create an entity from persisted datay
     *
     * @param array $data
     * @return static
     */
    public static function __set_state(array $data);

    /**
     * Check if the entity is not persisted yet.
     *
     * @return bool
     */
    public function isNew(): bool;


    /**
     * Bind a handler for an event.
     *
     * @param string   $event
     * @param callable $handler
     * @return $this
     */
    public function on(string $event, callable $handler);

    /**
     * Trigger an event.
     *
     * @param string $event
     * @param mixed  $payload
     * @return mixed
     */
    public function trigger(string $event, $payload = null);
}
