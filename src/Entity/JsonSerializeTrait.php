<?php

namespace Jasny\Entity;

use stdClass;
use DateTime;
use JsonSerializable;
use Jasny\Entity;

/**
 * Serialize an entity 
 */
trait JsonSerializeTrait
{
    /**
     * Cast entity to an array
     */
    abstract public function toArray(): array;
    
    /**
     * Prepare entity for JSON encoding
     * 
     * @return stdClass
     */
    public function jsonSerialize(): stdClass
    {
        if ($this instanceof Entity\WithLazyLoading) {
            $this->expand();
        }

        $object = (object)$this->toArray();
        $this->jsonSerializeCast($object);
        
        return $object;
    }
    
    /**
     * Cast value for json serialization.
     * 
     * @param mixed $input
     */
    protected function jsonSerializeCast(&$value)
    {
        if ($value instanceof DateTime) {
            $value = $value->format(\DateTime::ISO8601);
        }

        if ($value instanceof JsonSerializable) {
            $value = $value->jsonSerialize();
        }

        if ($value instanceof stdClass || is_array($value)) {
            foreach ($value as &$prop) {
                $this->jsonSerializeCast($prop); // Recursion
            }
        }
    }
}
