<?php

namespace Jasny\Entity;

use stdClass;
use Jasny\Entity\DynamicInterface;
use function Jasny\expect_type;

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
     *   $entity->set(['qux' => 100, 'clr' => 'red']);
     * </code>
     * 
     * @param string|array|stdClass $key     Property or set of values
     * @param mixed                 $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        expect_type($key, func_num_args() === 1 ? ['array', stdClass::class] : 'string');
        
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
        expect_type($data, ['array', stdClass::class]);
        
        $set = function($entity) use ($data) {
            foreach ($data as $key => $value) {
                if (
                    !preg_match('/^[a-zA-Z]\w+$/', $key) ||
                    (!property_exists($entity, $key) && !$entity instanceof DynamicInterface)
                ) {
                    continue;
                }
                
                $entity->$key = $value;
            }
        };
        $set->bindTo(null);
        
        $set($this);
    }
}
