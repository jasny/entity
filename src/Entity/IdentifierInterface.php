<?php

namespace Jasny\Entity;

use Jasny\EntityInterface;

/**
 * Entity has a unique identifier.
 */
interface IdentifierInterface extends EntityInterface
{
    /**
     * Get entity id.
     * 
     * @return mixed
     */
    public function getId();
}
