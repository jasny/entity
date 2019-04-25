<?php

declare(strict_types=1);

namespace Jasny\Entity\Event;

use Jasny\Entity\Entity;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Base class for all entity events.
 */
abstract class AbstractBase implements StoppableEventInterface
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
     * @var bool
     */
    protected $propagationStopped = false;

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
        return $this->subject;
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

    /**
     * Don't execute any listeners after this one.
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    /**
     * Is propagation stopped?
     *
     * @return bool
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
