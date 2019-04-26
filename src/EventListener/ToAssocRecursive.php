<?php

declare(strict_types=1);

namespace Jasny\Entity\EventListener;

use Improved as i;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Entity;
use Jasny\Entity\Event;
use SplObjectStorage;
use stdClass;
use function Jasny\object_get_properties;

/**
 * Turn entity into associative array done recursively, also turning child entities into associative arrays.
 * In case of a cross-reference (parent -> child -> parent), the property is removed.
 */
class ToAssocRecursive
{
    /**
     * Invoke the listener.
     *
     * @param Event\ToAssoc $event
     */
    public function __invoke(Event\ToAssoc $event): void
    {
        $payload = $event->getPayload();

        $list = new SplObjectStorage();
        $list[$event->getEntity()] = null;

        $assoc = $this->toAssocRecursive($payload, $list);

        $event->setPayload($assoc);
    }

    /**
     * Cast the entity to an associative array.
     *
     * @param Entity           $entity
     * @param SplObjectStorage $list    Entity / assoc map for entities that already have been converted
     * @return array|null
     */
    protected function toAssocEntity(Entity $entity, SplObjectStorage $list): array
    {
        $list[$entity] = null;

        $assoc = $this->toAssocRecursive($entity->toAssoc(), $list);
        $list[$entity] = $assoc;

        return $assoc;
    }

    /**
     * Recursively cast to associative arrays.
     *
     * @param iterable|object   $input
     * @param SplObjectStorage  $list   Entity / assoc map for entities that already have been converted
     * @return array
     */
    protected function toAssocRecursive($input, SplObjectStorage $list): array
    {
        $values = is_iterable($input)
            ? i\iterable_to_array($input)
            : object_get_properties($input, $input instanceof stdClass || $input instanceof DynamicEntity);

        foreach ($values as $key => &$value) {
            if ($value instanceof Entity) {
                $value = $list->contains($value) ? $list[$value] : $this->toAssocEntity($value, $list);

                if ($value === null && !is_int($key)) {
                    unset($values[$key]);
                }
            } elseif (is_iterable($value) || $value instanceof stdClass) {
                $value = $this->toAssocRecursive($value, $list);
            }
        }

        return $values;
    }
}
