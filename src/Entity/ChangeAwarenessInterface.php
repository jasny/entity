<?php

namespace Jasny\Entity;

use Jasny\EntityInterface;

/**
 * Entity knows if and which properties has changed
 */
interface ChangeAwarenessInterface extends EntityInterface
{
    /**
     * Mark the current values of the entity as being persisted.
     * Persisted mean that these values have been written to a DB, REST endpoint of other data store.
     * 
     * @return void
     */
    public function markAsPersisted();
    
    /**
     * Check if the entity is new
     * 
     * @return boolean
     */
    public function isNew(): bool;
    
    /**
     * Check if the entity is modified
     * 
     * @return boolean
     */
    public function isModified(): bool;
    
    /**
     * Check if a property has changed
     * 
     * @param string $property
     * @return boolean
     */
    public function hasModified(string $property): bool;
    
    
    /**
     * Get the values that have changed
     * 
     * @return array
     */
    public function getChanges(): array;
    
    /**
     * Get a copy of the entity without the modifications.
     * 
     * @return static
     */
    public function getUnmodifiedCopy(): self;
}
