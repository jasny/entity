<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\IdentifiableEntityInterface;

/**
 * Check if two entities are the same.
 */
trait CompareTrait
{
    /**
     * Check if entity is the same as the provided entity or id.
     *
     * @param EntityInterface|mixed $compare
     * @return bool
     */
    public function is($compare): bool
    {
        return $this === $compare || $this->hasId($compare) || $this->hasSameId($compare);
    }

    /**
     * @param mixed $compare
     * @return bool
     */
    private function hasId($compare): bool
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $this instanceof IdentifiableEntityInterface && $this->getId() === $compare;
    }

    /**
     * @param EntityInterface|mixed $compare
     * @return bool
     */
    private function hasSameId($compare): bool
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return
            $this instanceof IdentifiableEntityInterface &&
            $compare instanceof IdentifiableEntityInterface &&
            get_class($this) === get_class($compare) &&
            $this->getId() === $compare->getId();
    }
}
