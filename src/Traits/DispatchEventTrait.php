<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use BadMethodCallException;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Dispatch events emitted by the entity.
 */
trait DispatchEventTrait
{
    /**
     * @ignore
     * @var EventDispatcherInterface
     */
    private $i__dispatcher;


    /**
     * Set the event dispatcher
     *
     * @param EventDispatcherInterface $dispatcher
     * @return void
     */
    final public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        if (isset($this->i__dispatcher)) {
            throw new BadMethodCallException("Event dispatcher already set for this entity");
        }

        $this->i__dispatcher = $dispatcher;
    }

    /**
     * Get trigger for an event
     *
     * @return EventDispatcherInterface
     */
    final public function getEventDispatcher(): EventDispatcherInterface
    {
        if (!isset($this->i__dispatcher)) {
            throw new BadMethodCallException("Event dispatcher has not been set for this entity");
        }

        return $this->i__dispatcher;
    }

    /**
     * Dispatch an event.
     *
     * @param object $event
     * @return object  The event.
     */
    public function dispatchEvent(object $event): object
    {
        if (isset($this->i__dispatcher)) {
            $this->i__dispatcher->dispatch($event);
        }

        return $event;
    }
}
