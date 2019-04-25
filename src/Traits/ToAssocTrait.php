<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Improved as i;
use BadMethodCallException;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\Event;
use function Jasny\object_set_properties;
use function Jasny\object_get_properties;
use LogicException;
use stdClass;

/**
 * Get and set entity properties
 */
trait ToAssocTrait
{
    /**
     * Dispatch an event.
     *
     * @param object $event
     * @return object  The event.
     */
    abstract public function dispatchEvent(object $event): object;

    /**
     * Cast the entity to an associative array.
     *
     * @return array
     */
    public function toAssoc(): array
    {
        $values = object_get_properties($this, $this instanceof DynamicEntity);
        $this->toAssocRecursive($values);

        $updatedValues = $this->dispatchEvent(new Event\ToAssoc($this, $values))->getPayload();

        return $updatedValues;
    }

    /**
     * Recursively cast entities to associative arrays.
     *
     * @param array $values
     */
    protected function toAssocRecursive(array &$values): void
    {
        foreach ($values as &$value) {
            if ($value instanceof Entity) {
                $value = $value->toAssoc();
            } elseif (is_array($value) || $value instanceof stdClass) {
                $value = (array)$value;
                $this->toAssocRecursive($value);
            } elseif (is_iterable($value)) {
                $value = iterable_to_array($value);
                $this->toAssocRecursive($value);
            }
        }
    }
}
