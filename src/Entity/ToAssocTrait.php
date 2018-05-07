<?php

namespace Jasny\Entity;

use Jasny\Entity;

/**
 * Cast entity to an array
 */
trait ToAssocTrait
{
    /**
     * Cast the entity to an associative array.
     * 
     * @return array
     */
    public function toAssoc(): array
    {
        $values = call_user_func('get_object_vars', $this);
        
        foreach (array_keys($values) as $key) {
            if ($this instanceof Entity\WithCensoring && $this->hasCensored($key)) {
                unset($values[$key]);
            }
        }
        
        return $values;
    }
}
