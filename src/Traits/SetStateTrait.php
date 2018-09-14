<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\IdentifiableEntityInterface;
use Jasny\Entity\DynamicEntityInterface;
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
     * Create an entity from persisted data
     *
     * @param array $data
     * @return static
     * @throws \ReflectionException
     */
    public static function __set_state(array $data)
    {
        $class = get_called_class();
        $isDynamic = is_a($class, DynamicEntityInterface::class, true);

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
     * Mark entity as persisted
     *
     * @return $this
     */
    public function markAsPersisted(): self
    {
        $this->markNew(false);
        $this->trigger('persisted');

        return $this;
    }


    /**
     * Refresh with data from persisted storage.
     *
     * @param static $replacement
     * @return void
     * @throws InvalidArgumentException if replacement is a different entity
     */
    public function refresh(EntityInterface $replacement): void
    {
        expect_type($replacement, get_class($this));

        if ($this instanceof IdentifiableEntityInterface && !$this->is($replacement)) {
            $msg = sprintf(
                "Replacement %s is not the same entity; id %s doesn't match %s",
                get_class($this),
                $replacement instanceof IdentifiableEntityInterface ? json_encode($replacement->getId()) : '',
                json_encode($this->getId())
            );
            throw new \InvalidArgumentException($msg);
        }

        $replacement = $this->trigger("refresh.before", $replacement);
        expect_type($replacement, [EntityInterface::class, 'array'], \UnexpectedValueException::class);

        $data = $replacement instanceof EntityInterface ? $replacement->toAssoc() : $replacement;
        $isDynamic = $this instanceof DynamicEntityInterface;
        object_set_properties($this, $data, $isDynamic);

        $this->trigger("refresh.after", $data);
    }
}
