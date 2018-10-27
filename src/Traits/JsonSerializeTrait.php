<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\DynamicEntity;
use function Jasny\object_get_properties;
use function Jasny\expect_type;

/**
 * Entity json serialize implementation
 */
trait JsonSerializeTrait
{
    /**
     * Trigger before an event.
     *
     * @param string $event
     * @param mixed $payload
     * @return mixed|void
     */
    abstract public function trigger(string $event, $payload = null);


    /**
     * Prepare entity for JsonSerialize encoding
     *
     * @return \stdClass
     */
    public function jsonSerialize(): \stdClass
    {
        $isDynamic = $this instanceof DynamicEntity;
        $object = (object)object_get_properties($this, $isDynamic);

        $result = $this->trigger('jsonSerialize', $object);
        expect_type($result, \stdClass::class, \UnexpectedValueException::class);

        return $result;
    }
}
