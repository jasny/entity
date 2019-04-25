<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Improved as i;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Event;
use stdClass;
use UnexpectedValueException;
use function Jasny\object_get_properties;

/**
 * Entity json serialize implementation
 */
trait ToJsonTrait
{
    /**
     * Dispatch an event.
     *
     * @param object $event
     * @return object  The event.
     */
    abstract public function dispatchEvent(object $event): object;

    /**
     * Prepare entity for JsonSerialize encoding
     *
     * @return stdClass
     */
    public function jsonSerialize(): stdClass
    {
        $object = (object)object_get_properties($this, $this instanceof DynamicEntity);

        $result = $this->dispatchEvent(new Event\ToJson($this, $object))->getPayload();
        i\type_check($result, stdClass::class, new UnexpectedValueException());

        return $result;
    }
}