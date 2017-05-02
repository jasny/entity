<?php

namespace Jasny\Entity;

/**
 * Entity can be restored after deletion.
 */
interface SoftDeletion
{
    /**
     * Checks if entity has been deleted
     * 
     * @return boolean
     */
    public function isDeleted();
    
    /**
     * Restore deleted entity.
     * Does nothing is entity isn't deleted.
     * 
     * @return $this
     */
    public function undelete();
    
    /**
     * Purge deleted entity.
     * 
     * @return $this
     * @throws \RuntimeException if entity isn't deleted
     */
    public function purge();
}
