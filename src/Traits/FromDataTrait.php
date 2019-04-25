<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\DynamicEntity;
use function Jasny\object_set_properties;

/**
 * Entity::__set_state method
 */
trait FromDataTrait
{
    /**
     * @var bool
     */
    private $i__new = true;

    /**
     * Mark entity as new or persisted
     *
     * @param bool $state
     * @return void
     */
    final protected function markNew(bool $state): void
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
     * Create an entity from persisted data.
     *
     * @param array $data
     * @return static
     * @throws \ReflectionException
     */
    public static function fromData(array $data)
    {
        $class = get_called_class();
        $isDynamic = is_a($class, DynamicEntity::class, true);

        /** @var static $entity */
        $entity = (new \ReflectionClass($class))->newInstanceWithoutConstructor();

        object_set_properties($entity, $data, $isDynamic);

        if (method_exists($entity, '__construct')) {
            $entity->__construct();
        }

        $entity->markNew(false);

        return $entity;
    }

    /**
     * Alias of `fromData()`
     *
     * @param array $data
     * @return static
     * @throws \ReflectionException
     */
    final public static function __set_state(array $data)
    {
        return static::fromData($data);
    }

    /**
     * Mark entity as persisted.
     */
    public function markAsPersisted(): void
    {
        $this->markNew(false);
    }
}
