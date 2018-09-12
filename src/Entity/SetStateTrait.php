<?php

namespace Jasny\Entity;

use stdClass;

/**
 * Create a new entity from data
 */
trait SetStateTrait
{
    /**
     * Set the values of the public properties of this entity.
     * 
     * @param array|stdClass $data
     */
    abstract protected function setPublicProperties($data);
    
    /**
     * Create a new object without calling the constructor.
     * 
     * @return static
     */
    protected static function newInstanceWithoutConstructor()
    {
        $class = get_called_class();
        $reflection = new \ReflectionClass($class);
        
        return $reflection->newInstanceWithoutConstructor();
    }
    
    /**
     * Create an entity from the data representation.
     * Calls the construtor *after* setting the properties.
     * 
     * @param array|stdClass|mixed $data
     * @return static
     */
    public static function __set_state($data)
    {
        $entity = static::newInstanceWithoutConstructor();
        
        $entity->setPublicProperties($data);
        
        if (method_exists($entity, '__construct')) {
            $entity->__construct();
        }
        
        return $entity;
    }
}
