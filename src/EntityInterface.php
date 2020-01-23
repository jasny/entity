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
     * @param string|array<string,mixed> $key
     * @param mixed                      $value  Omit when passing an array
     * @return $this
     */
    public function set($key, $value = null);


    /**
     * Check if entity is the same as the provided entity or matches id or filter.
     *
     * @param static|mixed $compare  Entity or entity id
     * @return bool
     */
    public function is($compare): bool;


    /**
     * Check if the entity is not persisted yet.
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
     * @param array<string,mixed> $data
     * @return static
     */
    public static function __set_state(array $data);


    /**
     * Set the event dispatcher
     *
     * @param EventDispatcherInterface $dispatcher
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
     * @template TEvent of object
     * @phpstan-param TEvent $event
     * @phpstan-return TEvent
     */
    public function dispatchEvent(object $event): object;
}
