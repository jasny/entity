<?php

declare(strict_types=1);

namespace Jasny\Entity\Trigger;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\Handler\HandlerInterface;

/**
 * Triggers that should be applied to Entities.
 * @immutable
 */
class TriggerSet implements TriggerSetInterface
{
    /**
     * @var array
     */
    public $triggers = [];


    /**
     * Check if the trigger exists.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->triggers[$name]);
    }

    /**
     * Get a trigger.
     *
     * @param string $name
     * @return callable[]
     * @throws \OutOfBoundsException if trigger does not exist
     */
    public function get(string $name): array
    {
        if (!isset($this->triggers[$name])) {
            throw new \OutOfBoundsException("Set doesn't contain trigger '$name'");
        }

        return $this->triggers[$name];
    }

    /**
     * Assert that all triggers are a handler or callable.
     *
     * @param string $name
     * @param array  $triggers
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function assertTriggers(string $name, array $triggers): void
    {
        foreach ($triggers as $handler) {
            if (
                !$handler instanceof HandlerInterface &&
                !$handler instanceof \Closure &&
                (is_object($handler) || !is_callable($handler))
            ) {
                $type = (is_object($handler) ? get_class($handler) . ' ' : '') . gettype($handler);

                throw new \InvalidArgumentException("Unable to add '$name' trigger(s); " .
                    "Expected " . HandlerInterface::class . " or (non-object) callable, got a $type");
            }
        }
    }

    /**
     * Add trigger(s).
     *
     * @param string                        $name
     * @param HandlerInterface[]|callable[] $triggers
     * @return static
     */
    public function with(string $name, array $triggers): self
    {
        $this->assertTriggers($name, $triggers);

        $clone = clone $this;
        $clone->triggers[$name] = $triggers;

        return $clone;
    }

    /**
     * Remove trigger(s).
     *
     * @param string $name
     * @return static
     */
    public function without(string $name): self
    {
        if (!isset($this->triggers[$name])) {
            return $this;
        }

        $clone = clone $this;
        unset($clone->triggers[$name]);

        return $clone;
    }


    /**
     * Add triggers to entity.
     *
     * @param EntityInterface $entity
     * @return void
     */
    public function apply(EntityInterface $entity): void
    {
        foreach ($this->triggers as $triggers) {
            foreach ($triggers as $event => $handler) {
                $entity->on($event, $handler);
            }
        }
    }
}
