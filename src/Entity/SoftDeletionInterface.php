<?php

namespace Jasny\Entity;

use Jasny\EntityInterface;

/**
 * Entity can be restored after deletion.
 */
interface SoftDeletionInterface extends EntityInterface
{
    /**
     * Checks if entity has been deleted
     * 
     * @return boolean
     */
    public function isDeleted(): bool;
    
    /**
     * Restore deleted entity.
     * Does nothing is entity isn't deleted.
     * 
     * @return $this
     */
    public function undelete(): self;
    
    /**
     * Purge deleted entity.
     * 
     * @return $this
     * @throws \RuntimeException if entity isn't deleted
     */
    public function purge(): self;
}
