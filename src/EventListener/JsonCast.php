<?php

declare(strict_types=1);

namespace Jasny\Entity\EventListener;

use DateTime;
use DateTimeInterface;
use Improved as i;
use Jasny\Entity\Event;
use JsonSerializable;
use SplObjectStorage;
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
     */
    public function __invoke(Event\ToJson $event): void
    {
        $payload = $event->getPayload();

        $list = new SplObjectStorage();
        $list[$event->getEntity()] = null;

        $data = $this->cast($payload, $list);

        $event->setPayload($data);
    }


    /**
     * Cast value for json serialization.
     *
     * @param mixed            $input
     * @param SplObjectStorage $list   Entity / assoc map for entities that already have been converted
     * @return mixed
     */
    protected function cast($input, SplObjectStorage $list)
    {
        $value = $input instanceof JsonSerializable
            ? $input->jsonSerialize()
            : $input;

        if ($value instanceof DateTimeInterface) {
            $value = $value->format(DateTime::ISO8601);
        }

        if (is_iterable($value)) {
            $value = i\iterable_to_array($value, true);
        }

        if (is_array($value) || $value instanceof stdClass) {
            $value = $this->castRecursive($input, $value, $list);
        }

        return $value;
    }

    /**
     * Cast value recursively.
     *
     * @param mixed            $source
     * @param array|stdClass   $values
     * @param SplObjectStorage $list    Entity / assoc map for entities that already have been converted
     * @return array|stdClass
     */
    protected function castRecursive($source, $values, SplObjectStorage $list)
    {
        if (is_object($source)) {
            $list[$source] = null;
        }

        foreach ($values as $key => &$value) {
            if (!is_object($value) || !$list->contains($value)) {
                $value = $this->cast($value, $list); // Recursion
            } elseif ($list[$value] === null && is_object($values)) {
                unset($values->$key);
            } elseif ($list[$value] === null && is_array($values) && !is_int($key)) {
                unset($values[$key]);
            } else {
                $value = $list[$value];
            }
        }

        if (is_object($source)) {
            $list[$source] = $values;
        }

        return $values;
    }
}
