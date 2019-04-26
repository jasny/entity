<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Improved as i;
use BadMethodCallException;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Event;
use function Jasny\object_set_properties;
use LogicException;

/**
 * Set entity properties.
 */
trait SetTrait
{
    /**
     * Assert that the object isn't a ghost.
     *
     * @throws LogicException if object is a ghost
     */
    abstract protected function assertNotGhost(): void;

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
     * @param string|array $key Property or set of values
     * @param mixed $value
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

        $this->assertNotGhost();

        $input = func_num_args() === 1 ? (array)$key : [$key => $value];
        $data = $this->dispatchEvent(new Event\BeforeSet($this, $input))->getPayload();

        object_set_properties($this, $data, $this instanceof DynamicEntity);

        $this->dispatchEvent(new Event\AfterSet($this, $data));

        return $this;
    }
}
