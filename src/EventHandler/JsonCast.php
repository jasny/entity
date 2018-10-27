<?php

declare(strict_types=1);

namespace Jasny\Entity\EventHandler;

use Jasny\Entity\Entity;
use function Jasny\expect_type;
use function Jasny\iterable_map;
use function Jasny\iterable_to_array;

/**
 * Cast for JSON serialize
 */
class JsonCast implements EventHandlerInterface
{
    /**
     * Invoke the handler as callback.
     *
     * @param Entity $entity
     * @param \stdClass       $data
     * @return \stdClass
     */
    public function __invoke(Entity $entity, $data = null)
    {
        expect_type($data, \stdClass::class);

        return $this->cast($data);
    }

    /**
     * Cast value for json serialization.
     *
     * @param mixed $value
     * @param int   $deep
     * @return mixed
     */
    protected function cast($value, int $deep = 0)
    {
        if ($deep >= 100) {
            throw new \RuntimeException("Maximum recursion nesting level of '100' reached");
        }

        switch (true) {
            case $value instanceof \DateTime:
                return $value->format(\DateTime::ISO8601);
            case $value instanceof \JsonSerializable:
                return $value->jsonSerialize();
            case is_iterable($value):
                return $this->castIterable($value, $deep);
            case $value instanceof \stdClass:
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
        $mapped = iterable_map($value, function($value) use ($deep) {
            return $this->cast($value, $deep + 1);
        });

        return iterable_to_array($mapped);
    }

    /**
     * Cast stdClass object
     *
     * @param \stdClass $value
     * @param int       $deep
     * @return mixed
     */
    protected function castObject(\stdClass $value, int $deep)
    {
        foreach ($value as &$prop) {
            $prop = $this->cast($prop, $deep + 1); // Recursion
        }

        return $value;
    }
}
