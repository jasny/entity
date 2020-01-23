<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\Entity;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Event;
use function Jasny\object_get_properties;

/**
 * Entity json serialize implementation.
 *
 * @implements Entity
 */
trait ToJsonTrait
{
    /**
     * Dispatch an event.
     *
     * @param object $event
     * @return object  The event.
     *
     * @template T
     * @phpstan-param T $event
     * @phpstan-return T
     */
    abstract public function dispatchEvent(object $event): object;

    /**
     * Prepare entity for JsonSerialize encoding
     *
     * @return \stdClass
     */
    public function jsonSerialize(): \stdClass
    {
        $object = (object)object_get_properties($this, $this instanceof DynamicEntity);

        /** @var Entity $this */
        return $this->dispatchEvent(new Event\ToJson($this, $object))->getPayload();
    }
}
