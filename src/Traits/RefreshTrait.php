<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Improved as i;
use InvalidArgumentException;
use Jasny\Entity\DynamicEntity;
use Jasny\Entity\Event;
use Jasny\Entity\Entity;
use Jasny\Entity\IdentifiableEntity;
use function Jasny\object_set_properties;
use UnexpectedValueException;

trait RefreshTrait
{
    /**
     * Mark entity as persisted.
     */
    abstract public function markAsPersisted(): void;

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
        i\type_check($replacement, get_class($this));

        if ($this instanceof IdentifiableEntity && !$this->is($replacement)) {
            $msg = sprintf(
                "Replacement %s is not the same entity; id %s doesn't match %s",
                get_class($this),
                $replacement instanceof IdentifiableEntity ? json_encode($replacement->getId()) : '',
                json_encode($this->getId())
            );
            throw new InvalidArgumentException($msg);
        }

        $replacementData = i\type_check(
            $this->dispatchEvent(new Event\BeforeRefresh($this, $replacement))->getPayload(),
            [Entity::class, 'array'],
            new UnexpectedValueException('Event listener(s) changed replacement into %s')
        );

        $data = $replacementData instanceof Entity ? $replacementData->toAssoc() : $replacementData;
        object_set_properties($this, $data, $this instanceof DynamicEntity);

        if (!$replacement->isNew()) {
            $this->markAsPersisted();
        }

        $this->dispatchEvent(new Event\AfterRefresh($this, $data));
    }

}
