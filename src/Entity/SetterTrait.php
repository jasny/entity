<?php

namespace Jasny\Entity;

use stdClass;
use Jasny\Entity;
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
        expect_type($data, ['array', stdClass::class]);
        
        $set = function($entity) use ($data) {
            foreach ($data as $key => $value) {
                if (
                    !preg_match('/^[a-zA-Z]\w+$/', $key) ||
                    (!property_exists($entity, $key) && !$entity instanceof Entity\WithDynamicProperties)
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
