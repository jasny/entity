<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\DynamicEntityInterface;
use Jasny\Entity\Event;
use function Jasny\object_get_properties;

/**
 * EntityInterface json serialize implementation.
 *
 * @implements EntityInterface
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
        $object = (object)object_get_properties($this, $this instanceof DynamicEntityInterface);

        /** @var EntityInterface $this */
        return $this->dispatchEvent(new Event\ToJson($this, $object))->getPayload();
    }
}
