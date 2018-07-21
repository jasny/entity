<?php

namespace Jasny\Entity\Traits;

use ReflectionClass;
use Jasny\Entity\DynamicInterface;
use function Jasny\object_set_properties;

/**
 * Entity::__set_state method
 *
 * @author  Arnold Daniels <arnold@jasny.net>
 * @license https://raw.github.com/jasny/entity/master/LICENSE MIT
 * @link    https://jasny.github.com/entity
 */
trait SetStateTrait
{
    /**
     * @var bool
     */
    private $i__new = true;


    /**
     * Trigger before an event.
     *
     * @param string $event
     * @param mixed $payload
     * @return mixed|void
     */
    abstract public function trigger(string $event, $payload = null);


    /**
     * Mark entity as new or persisted
     *
     * @param bool $state
     */
    final protected function markNew(bool $state)
    {
        $this->i__new = $state;
    }

    /**
     * Check if the entity is not persisted yet.
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->i__new;
    }

    /**
     * Create an entity from persisted data
     *
     * @param array $data
     * @return static
     * @throws \ReflectionException
     */
    public static function __set_state(array $data)
    {
        $class = get_called_class();
        $isDynamic = is_a($class, DynamicInterface::class, true);
        $entity = (new ReflectionClass($class))->newInstanceWithoutConstructor();

        object_set_properties($entity, $data, $isDynamic);

        if (method_exists($entity, '__construct')) {
            $entity->__construct();
        }

        $entity->markNew(false);

        return $entity;
    }

    /**
     * Mark entity as persisted
     *
     * @return this
     */
    public function markAsPersisted(): self
    {
        $this->markNew(false);
        $this->trigger('persisted');

        return $this;
    }
}
