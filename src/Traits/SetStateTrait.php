<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use InvalidArgumentException;
use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\DynamicEntity;
use function Jasny\object_set_properties;
use function Jasny\expect_type;

/**
 * Entity::__set_state method
 */
trait SetStateTrait
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
    public static function __set_state(array $data)
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
     * Mark entity as persisted.
     */
    public function markAsPersisted(): self
    {
        $this->markNew(false);
    }
}
