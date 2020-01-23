<?php

declare(strict_types=1);

namespace Jasny\Entity\Event;

use Jasny\Entity\Entity;

/**
 * Base class for all entity events.
 */
abstract class AbstractEvent
{
    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var mixed
     */
    protected $payload;

    /**
     * Class constructor.
     *
     * @param Entity $entity   Entity that emitted the event
     * @param mixed  $payload  Initial payload
     */
    public function __construct(Entity $entity, $payload = null)
    {
        $this->entity = $entity;
        $this->payload = $payload;
    }

    /**
     * Get the entity that emitted the event.
     *
     * @return Entity
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * Change the event payload.
     *
     * @param mixed $payload
     */
    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }

    /**
     * Get the event payload.
     *
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
