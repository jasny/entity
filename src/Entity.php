<?php

declare(strict_types=1);

namespace Jasny\Entity;

/**
 * An entity is an object with a (persistent) data representation.
 */
interface Entity extends \JsonSerializable
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
     * Cast the entity to an associative array.
     *
     * @return array
     */
    public function toAssoc(): array;


    /**
     * Check if entity is the same as the provided entity or matches id or filter.
     *
     * @param Entity|array|mixed $filter
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


    /**
     * Create an entity from persisted data.
     * @internal
     *
     * @param array $data
     * @return static
     */
    public static function __set_state(array $data);

    /**
     * Refresh with data from persisted storage.
     *
     * @param static|array $replacement
     * @return void
     */
    public function refresh($replacement): void;
}
