<?php

namespace Jasny\Entity;

use stdClass;
use InvalidArgumentException;

/**
 * Set properties of the entity
 */
trait SetterTrait
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
     * @param string|array|stdClass $key     Property or set of values
     * @param mixed                 $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        $values = func_num_args() === 1 ? $key : [$key => $value];
        $this->setPublicProperties($values);
        
        return $this;
    }
    
    /**
     * Set the values of the public properties of this entity.
     * 
     * @internal Using closure to prevent setting protected methods.
     * 
     * @param array|stdClass $data
     */
    protected function setPublicProperties($data)
    {
        validate_argument($data, ['array', stdClass::class]);
        
        if (!is_array($data) && !$data instanceof stdClass) {
            $type = (is_object($data) ? get_class($data) . ' ' : '') . gettype($data);
            throw new InvalidArgumentException("Expected an array or stdClass object, but got a $type");
        }
        
        $set = function($entity) use ($data) {
            foreach ($data as $key => $value) {
                if (!ctype_alpha($key[0]) || (!property_exists($entity, $key) && !$entity instanceof Dynamic)) {
                    continue;
                }
                
                $entity->$key = $value;
            }
        };
        $set->bindTo(null);
        
        $set($this);
    }
}
