<?php

declare(strict_types=1);

namespace Jasny\Entity;

use JsonSerializable;
use LogicException;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * An entity is an object with a (persistent) data representation.
 */
interface EntityInterface extends JsonSerializable
{
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
     * Check if entity is the same as the provided entity or matches id or filter.
     *
     * @param EntityInterface|array|mixed $filter
     * @return bool
     */
    public function is($filter): bool;


    /**
     * Check if the entity is not persisted yet.
     *
     * @return bool
     */
    public function isNew(): bool;

    /**
     * Cast an entity to an associative array.
     *
     * @return array<string,mixed>
     */
    public function __serialize(): array;

    /**
     * Load persisted data into an entity
     *
     * @param array<string,mixed> $data
     */
    public function __unserialize(array $data): void;

    /**
     * Create an entity from persisted data.
     *
     * @param array $data
     * @return static
     */
    public static function __set_state(array $data);


    /**
     * Set the event dispatcher
     *
     * @param EventDispatcherInterface $dispatcher
     * @return void
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void;

    /**
     * Get the event dispatcher
     *
     * @return EventDispatcherInterface
     * @throws LogicException if event dispatcher isn't set
     */
    public function getEventDispatcher(): EventDispatcherInterface;

    /**
     * Dispatch an event.
     *
     * @param object $event
     * @return object  The event.
     *
     * @template T
     * @phpstan-param T $event
     * @phpstan-return T
     */
    public function dispatchEvent(object $event): object;
}
