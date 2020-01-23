<?php

declare(strict_types=1);

namespace Jasny\Entity\Event;

use Jasny\Entity\EntityInterface;

/**
 * Base class for all entity events.
 */
abstract class AbstractEvent
{
    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * @var mixed
     */
    protected $payload;

    /**
     * Class constructor.
     *
     * @param EntityInterface $entity   EntityInterface that emitted the event
     * @param mixed  $payload  Initial payload
     */
    public function __construct(EntityInterface $entity, $payload = null)
    {
        $this->entity = $entity;
        $this->payload = $payload;
    }

    /**
     * Get the entity that emitted the event.
     *
     * @return EntityInterface
     */
    public function getEntity(): EntityInterface
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
