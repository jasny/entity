<?php

declare(strict_types=1);

namespace Jasny\Entity\Handler;

use Jasny\Entity\EntityInterface;
use function Jasny\expect_type;

/**
 * Cast for JSON serialize
 */
class JsonCast implements HandlerInterface
{
    /**
     * Invoke the handler as callback.
     *
     * @param EntityInterface $entity
     * @param \stdClass       $data
     * @return \stdClass
     */
    public function __invoke(EntityInterface $entity, $data = null)
    {
        expect_type($data, \stdClass::class);

        return $this->cast($data);
    }

    /**
     * Cast value for json serialization.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function cast($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format(\DateTime::ISO8601);
        }

        if ($value instanceof \JsonSerializable) {
            return $value->jsonSerialize();
        }

        if (!is_array($value) && is_iterable($value)) {
            $value = iterator_to_array($value);
        }

        if ($value instanceof \stdClass || is_array($value)) {
            foreach ($value as &$prop) {
                $prop = $this->cast($prop); // Recursion
            }
        }

        return $value;
    }
}
