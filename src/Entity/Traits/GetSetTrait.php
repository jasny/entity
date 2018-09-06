<?php

namespace Jasny\Entity\Traits;

use Jasny\Entity\DynamicInterface;
use function Jasny\expect_type;
use function Jasny\object_set_properties;
use function Jasny\object_get_properties;

/**
 * Get and set entity properties
 *
 * @author  Arnold Daniels <arnold@jasny.net>
 * @license https://raw.github.com/jasny/entity/master/LICENSE MIT
 * @link    https://jasny.github.com/entity
 */
trait GetSetTrait
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
     * Cast the entity to an associative array.
     *
     * @return array
     */
    public function toAssoc(): array
    {
        $values = object_get_properties($this);

        return $this->trigger('toAssoc', $values);
    }

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
        expect_type($key, ['array', 'string']);

        if (func_num_args() === 1 && is_string($key)) {
            throw new \BadMethodCallException("Too few arguments to method " . __METHOD__ . "(), " .
                "if first argument is a string, a second argument is required");
        }

        $payload = func_num_args() === 1 ? (array)$key : [$key => $value];

        $data = $this->trigger('before:set', $payload);
        $isDynamic = $this instanceof DynamicInterface;

        object_set_properties($this, $data, $isDynamic);

        $this->trigger('after:set', $data);

        return $this;
    }
}
