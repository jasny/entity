<?php

declare(strict_types=1);

namespace Jasny\Entity\Trigger;

use Jasny\Entity\EntityInterface;

/**
 * Interface for triggers that should be applied to new Entities.
 */
interface TriggerSetInterface
{
    /**
     * Check if the trigger exists.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Get a trigger.
     *
     * @param string $name
     * @return callable[]
     */
    public function get(string $name): array;

    /**
     * Add trigger(s).
     *
     * @param string     $name
     * @param callable[] $triggers
     * @return static
     */
    public function with(string $name, array $triggers);

    /**
     * Remove trigger(s).
     *
     * @param string $name
     * @return static
     */
    public function without(string $name);

    /**
     * Add triggers to entity.
     *
     * @param EntityInterface $entity
     */
    public function apply(EntityInterface $entity): void;
}
