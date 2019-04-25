<?php

declare(strict_types=1);

namespace Jasny\Entity\EventListener;

use DateTime;
use DateTimeInterface;
use Improved as i;
use Jasny\Entity\Event;
use JsonSerializable;
use stdClass;

/**
 * Cast for JSON serialize
 */
class JsonCast
{
    /**
     * Invoke the handler as callback.
     *
     * @param Event\ToJson $event
     * @return \stdClass
     * @throws \RuntimeException if there is a circular reference
     */
    public function __invoke(Event\ToJson $event)
    {
        $data = $event->getPayload();

        return $this->cast($data);
    }

    /**
     * Cast value for json serialization.
     *
     * @param mixed $value
     * @param int   $deep
     * @return mixed
     * @throws \RuntimeException if there is a circular reference
     */
    protected function cast($value, int $deep = 0)
    {
        if ($deep >= 100) {
            throw new \RuntimeException("Maximum recursion nesting level of '100' reached");
        }

        switch (true) {
            case $value instanceof DateTimeInterface:
                return $value->format(DateTime::ISO8601);
            case $value instanceof JsonSerializable:
                return $value->jsonSerialize();
            case is_iterable($value):
                return $this->castIterable($value, $deep);
            case $value instanceof stdClass:
                return $this->castObject($value, $deep);
            default:
                return $value;
        }
    }

    /**
     * Cast iterable or object
     *
     * @param iterable $value
     * @param int      $deep
     * @return mixed
     */
    protected function castIterable(iterable $value, int $deep)
    {
        $mapped = i\iterable_map($value, function($value) use ($deep) {
            return $this->cast($value, $deep + 1);
        });

        return i\iterable_to_array($mapped);
    }

    /**
     * Cast stdClass object
     *
     * @param \stdClass $value
     * @param int       $deep
     * @return mixed
     * @throws \RuntimeException if there is a circular reference
     */
    protected function castObject(\stdClass $value, int $deep)
    {
        foreach ($value as &$prop) {
            $prop = $this->cast($prop, $deep + 1); // Recursion
        }

        return $value;
    }
}
