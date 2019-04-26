<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Event;
use function Jasny\object_get_properties;

/**
 * Get entity properties as associative array.
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
        $assoc = object_get_properties($this, $this instanceof DynamicEntity);

        return $this->dispatchEvent(new Event\ToAssoc($this, $assoc))->getPayload();
    }
}
