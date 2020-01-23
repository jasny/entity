<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Improved as i;
use Jasny\Entity\DynamicEntityInterface;
use Jasny\Entity\EntityInterface;
use Jasny\Entity\Event;
use function Jasny\object_set_properties;

/**
 * Set entity properties.
 */
trait SetTrait
{
    /**
     * Dispatch an event.
     *
     * @param object $event
     * @return object  The event.
     */
    abstract public function dispatchEvent(object $event): object;

    /**
     * Set a value or multiple values.
     *
     * <code>
     *   $entity->set('foo', 22);
     *   $entity->set('bar', null);
     *   $entity->set(['qux' => 100, 'clr' => 'red']);
     * </code>
     *
     * @param string|array<string,mixed> $key
     * @param mixed                      $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        i\type_check($key, ['array', 'string']);

        if (func_num_args() < 2 && is_string($key)) {
            throw new \ArgumentCountError(sprintf(
                "Too few arguments to method %s::%s(). If first argument is a string, second argument is required",
                __CLASS__,
                __FUNCTION__
            ));
        }

        if (func_num_args() >= 2 && is_array($key)) {
            throw new \ArgumentCountError(sprintf(
                "Too many arguments to method %s::%s(). If first argument is an array, second argument must be omitted",
                __CLASS__,
                __FUNCTION__
            ));
        }

        /** @var array<string,mixed> $data */
        $data = func_num_args() < 2 ? (array)$key : [$key => $value];
        $this->setProperties($data);

        return $this;
    }

    /**
     * @param array<string,mixed> $input
     */
    private function setProperties(array $input): void
    {
        /** @var Event\BeforeSet $event */
        /** @var self&EntityInterface $this */
        $event = $this->dispatchEvent(new Event\BeforeSet($this, $input));

        /** @var array<string,mixed> $data */
        $data = i\type_check($event->getPayload(), 'array');

        object_set_properties($this, $data, $this instanceof DynamicEntityInterface);

        /** @var self&EntityInterface $this */
        $this->dispatchEvent(new Event\AfterSet($this, $data));
    }
}
