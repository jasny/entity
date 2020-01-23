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
        if (!$this instanceof IdentifiableEntityInterface) {
            return false;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        return $this->scalarToString($this->getId()) === $this->scalarToString($compare);
    }

    /**
     * @param EntityInterface|mixed $compare
     * @return bool
     */
    private function hasSameId($compare): bool
    {
        return is_object($compare) && get_class($this) === get_class($compare) &&
            $compare instanceof IdentifiableEntityInterface && $this->hasId($compare->getId());
    }

    /**
     * Cast any scalar to a string (for non-strict comparison).
     *
     * @param mixed $value
     * @return mixed
     */
    private function scalarToString($value)
    {
        if (is_scalar($value)) {
            return (string)$value;
        }

        if (is_array($value)) {
            foreach ($value as &$item) {
                $item = $this->scalarToString($item);
            }
        }

        return $value;
    }
}
