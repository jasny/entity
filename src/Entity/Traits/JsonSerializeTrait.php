<?php

namespace Jasny\Entity\Traits;

use stdClass;
use DateTime;
use JsonSerializable;
use function Jasny\object_get_properties;

/**
 * Entity json serialize implementation
 *
 * @author  Arnold Daniels <arnold@jasny.net>
 * @license https://raw.github.com/jasny/entity/master/LICENSE MIT
 * @link    https://jasny.github.com/entity
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
     * @param EntityInterface $entity
     * @return stdClass
     */
    public function jsonSerialize(): stdClass
    {
        $object = (object)object_get_properties($this);
        $object = $this->jsonSerializeCast($object);

        return $this->trigger('jsonSerialize', $object);
    }

    /**
     * Cast value for json serialization.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function jsonSerializeCast($value)
    {
        if ($value instanceof DateTime) {
            return $value->format(DateTime::ISO8601);
        }

        if ($value instanceof JsonSerializable) {
            return $value->jsonSerialize();
        }

        if (!is_array($value) && is_iterable($value)) {
            $value = iterator_to_array($value);
        }

        if ($value instanceof stdClass || is_array($value)) {
            foreach ($value as &$prop) {
                $prop = $this->jsonSerializeCast($prop); // Recursion
            }
        }

        return $value;
    }
}
