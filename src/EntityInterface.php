<?php

namespace Jasny;

use JsonSerializable;

/**
 * An entity is an object with a (persistent) data representation.
 * 
 * @author  Arnold Daniels <arnold@jasny.net>
 * @license https://raw.github.com/jasny/db/master/LICENSE MIT
 * @link    https://jasny.github.com/db
 */
interface EntityInterface extends JsonSerializable
{
    /**
     * Set a value or multiple values.
     * 
     * <code>
     *   $entity->set('foo', 22);
     *   $entity->set('bar', null);
     *   $entity->set(['qux' => 100, '
     * </code>
     * 
     * @param string|array|object $key
     * @param mixed        $value
     * @return $this
     */
    public function set($key, $value);
    
    /**
     * Cast the entity to an associative array.
     * 
     * @return array
     */
    public function toArray(): array;
    
    /**
     * Convert data into an entity.
     * Calls the construtor *after* setting the properties.
     * 
     * @param array|stdClass|mixed $data  Data representation (from data source)
     * @return static
     */
    public static function __set_state($data);
}
