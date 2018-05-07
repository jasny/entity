<?php

namespace Jasny\Entity;

use Jasny\Entity\Comparator\Factory as ComparatorFactory;
use SebastianBergmann\Comparator\ComparisonFailure;
use Jasny\Meta\Introspection;

/**
 * Implementation for change aware entities.
 */
trait ChangeAwarenessTrait
{
    /**
     * @var static
     */
    private $i__persisted;

    /**
     * Mark the current values of the entity as being persisted.
     * Persisted mean that these values have been written to a DB, REST endpoint of other data store.
     */
    public function markAsPersisted()
    {
        $this->i__persisted = clone $this;
    }
    
    
    /**
     * Check if the entity is new
     * 
     * @return boolean
     */
    public function isNew()
    {
        return !isset($this->i__persisted);
    }
    
    /**
     * Check if the entity is modified
     * 
     * @return boolean
     */
    public function isModified()
    {
        return $this->compareHasModified($this->i__persisted, $this);
    }
    
    /**
     * Check if a property has changed
     * 
     * @param string $property
     * @return boolean
     */
    public function hasModified(string $property)
    {
        $original = isset($this->i__persisted->$property) ? $this->i__persisted->$property : null;
        $current = isset($this->$property) ? $this->$property : null;
        
        return $this->compareHasModified($original, $current);
    }
    
    /**
     * Get the comparator factory
     * 
     * @return ComparatorFactory
     */
    protected function getComparatorFactory()
    {
        return new Comparator\Factory();
    }
    
    /**
     * Compare the original value with the current value.
     * 
     * @param mixed $original
     * @param mixed $current
     * @return boolean
     */
    protected function compareHasModified($original, $current)
    {
        if ($original === $current) {
            return false;
        }
        
        $factory = $this->getComparatorFactory();
        $comparator = $factory->getComparatorFor($original, $current);
        
        try {
            $comparator->assertEquals($original, $current);
        } catch (ComparisonFailure $failure) {
            return true;
        }
        
        return false;        
    }
    
    
    /**
     * Get the values that have changed
     * 
     * @return array
     */
    public function getChanges(): array
    {
        $changes = [];
        $values = call_user_func('get_object_vars', $this);
        
        foreach ($values as $prop => $value) {
            if ($this instanceof Introspection && static::meta()->ofProperty($prop)['ignore']) {
                continue;
            }
            
            if ($this->hasModified($prop)) {
                $changes[$prop] = $value;
            }
        }
        
        return $changes;
    }
    
    /**
     * Get a copy of the entity without the modifications.
     * 
     * @return static|null
     */
    public function getUnmodifiedCopy()
    {
        return isset($this->i__persisted) ? clone $this->i__persisted : null;
    }
}
