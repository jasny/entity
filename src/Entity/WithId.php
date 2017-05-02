<?php

namespace Jasny\Entity;

/**
 * Entity has a unique identifier
 */
interface WithId
{
    /**
     * Get entity id.
     * 
     * @return mixed
     */
    public function getId();
    
    /**
     * Get identity property
     */
    public static function getIdProperty();
}
