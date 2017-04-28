<?php

namespace Jasny\Entity;

/**
 * Entity knows if and which properties has changed
 */
interface WithChangeAwareness
{
    /**
     * Check if the entity is new
     * 
     * @return boolean
     */
    public function isNew();
    
    /**
     * Check if the entity is modified
     * 
     * @return boolean
     */
    public function isModified();
    
    /**
     * Check if a property has changed
     * 
     * @param string $property
     * @return boolean
     */
    public function hasModified($property);
    
    
    /**
     * Get the values that have changed
     * 
     * @return array
     */
    public function getChanges();
    
    /**
     * Get a copy of the entity without the modifications.
     * 
     * @return static
     */
    public function getUnmodifiedCopy();
}
