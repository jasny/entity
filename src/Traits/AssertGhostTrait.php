<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\IdentifiableEntity;
use LogicException;

/**
 * Assertion required by multiple methods.
 */
trait AssertGhostTrait
{
    /**
     * Assert that the object isn't a ghost.
     *
     * @throws LogicException if object is a ghost
     */
    protected function assertNotGhost(): void
    {
        if (!$this instanceof IdentifiableEntity || !$this->isGhost()) {
            return;
        }

        $id = $this->getId();
        $class = get_class($this);

        throw new LogicException(sprintf('Trying to use ghost object as full object of %s entity "%s"', $class, $id));
    }
}
