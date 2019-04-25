<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use InvalidArgumentException;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Event;
use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use function Jasny\object_set_properties;

trait RefreshTrait
{
    /**
     * Dispatch an event.
     *
     * @param object $event
     * @return object  The event.
     */
    abstract public function dispatchEvent(object $event): object;

    /**
     * Refresh with data from persisted storage.
     *
     * @param static $replacement
     * @return void
     * @throws InvalidArgumentException if replacement is a different entity
     */
    public function refresh($replacement): void
    {
        expect_type($replacement, get_class($this));

        if ($this instanceof IdentifiableEntity && !$this->is($replacement)) {
            $msg = sprintf(
                "Replacement %s is not the same entity; id %s doesn't match %s",
                get_class($this),
                $replacement instanceof IdentifiableEntity ? json_encode($replacement->getId()) : '',
                json_encode($this->getId())
            );
            throw new InvalidArgumentException($msg);
        }

        $replacement = $this->dispatchEvent(new Event\BeforeRefresh($this, $replacement))->getPayload();
        expect_type($replacement, [Entity::class, 'array'], \UnexpectedValueException::class);

        $data = $replacement instanceof Entity ? $replacement->toAssoc() : $replacement;
        object_set_properties($this, $data, $this instanceof DynamicEntity);

        $this->

        $this->dispatchEvent(new Event\AfterRefresh($this, $data));
    }

}