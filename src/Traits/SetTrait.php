<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Improved as i;
use BadMethodCallException;
use Jasny\Entity\DynamicEntityInterface;
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

        if (func_num_args() === 1 && is_string($key)) {
            throw new BadMethodCallException(sprintf(
                "Too few arguments to method %s::%s(). If first argument is a string, a second argument is required",
                __CLASS__,
                __FUNCTION__
            ));
        }

        $input = func_num_args() === 1 ? (array)$key : [$key => $value];

        /** @var Event\BeforeSet $event */
        $event = $this->dispatchEvent(new Event\BeforeSet($this, $input));
        $data = $event->getPayload();

        object_set_properties($this, $data, $this instanceof DynamicEntityInterface);

        $this->dispatchEvent(new Event\AfterSet($this, $data));

        return $this;
    }
}
