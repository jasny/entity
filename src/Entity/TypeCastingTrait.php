<?php

namespace Jasny\Entity;

use Jasny\Meta;

/**
 * Use type casting for entities.
 */
trait TypeCastingTrait
{
    use Meta\TypeCasting\Implementation;
    
    /**
     * Get type cast object
     * 
     * @return DB\TypeCast
     */
    protected function typeCast($value)
    {
        $typecast = Entity\TypeCast::value($value);
        
        $typecast->alias('self', get_class($this));
        $typecast->alias('static', get_class($this));
        
        return $typecast;
    }
}
