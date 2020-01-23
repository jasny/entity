<?php

declare(strict_types=1);

namespace Jasny\Entity;

/**
 * EntityInterface with an id property.
 */
interface IdentifiableEntityInterface extends EntityInterface
{
    /**
     * Get entity id.
     *
     * @return mixed
     */
    public function getId();
}
