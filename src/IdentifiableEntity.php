<?php

declare(strict_types=1);

namespace Jasny\Entity;

/**
 * Entity with an id property.
 */
interface IdentifiableEntity extends Entity
{
    /**
     * Get entity id.
     *
     * @return mixed
     */
    public function getId();
}
