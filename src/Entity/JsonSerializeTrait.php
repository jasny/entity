<?php

namespace Jasny\Entity;

use stdClass;
use DateTime;
use JsonSerializable;
use ReflectionClass;
use Jasny\Entity\LazyLoadingInterface;

/**
 * Serialize an entity 
 */
trait JsonSerializeTrait
{
    /**
     * Prepare entity for JSON encoding
     * 
     * @return stdClass
     */
    public function jsonSerialize(): stdClass
    {
        if ($this instanceof LazyLoadingInterface) {
            $this->expand();
        }

        $object = (object)call_user_func('get_object_vars', $this); // Public properties only
        
        $this->jsonSerializeCast($object);
        $this->jsonSerializeFilter($object);
        
        return $object;
    }
    
    /**
     * Cast properties for json serialization.
     * 
     * @param mixed $input
     */
    protected function jsonSerializeCast(&$input)
    {
        foreach ($input as &$value) {
            if ($value instanceof DateTime) {
                $value = $value->format(\DateTime::ISO8601);
            }
            
            if ($value instanceof JsonSerializable) {
                $value = $value->jsonSerialize();
            }

            if ($value instanceof stdClass || is_array($value)) {
                $this->jsonSerializeCast($value); // Recursion
            }
        }
    }
    
    /**
     * Filter object for json serialization.
     * This method will call other methods that start with jsonSerializeFilter (in no particular order)
     * 
     * @param stdClass $object
     */
    protected function jsonSerializeFilter(stdClass &$object)
    {
        $refl = new ReflectionClass($this);
        
        foreach ($refl->getMethods() as $method) {
            if (strpos($method->getName(), __FUNCTION__) === 0 && $method->getName() !== __FUNCTION__) {
                $fn = $method->getName();
                $this->$fn($object);
            }
        }
    }
}
