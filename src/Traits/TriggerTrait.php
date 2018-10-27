<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\Trigger\EventDispatcher;

/**
 * Entity triggers implementation
 */
trait TriggerTrait
{
    /**
     * @ignore
     * @var EventDispatcher
     */
    private $i__dispatcher;


    /**
     * Set the event dispatcher
     *
     * @param EventDispatcher $dispatcher
     * @return void
     */
    final public function setEventDispatcher(EventDispatcher $dispatcher): void
    {
        if (isset($dispatcher)) {
            throw new \BadMethodCallException("Event dispatcher is already set");
        }

        $this->i__dispatcher = $dispatcher;
    }

    /**
     * Get trigger for an event
     *
     * @return EventDispatcher
     */
    final protected function getEventDispatcher(): EventDispatcher
    {
        if (!isset($this->i__dispatcher)) {
            throw new \BadMethodCallException("Event dispatcher has not been set");
        }

        return $this->i__dispatcher;
    }


    /**
     * Bind a callback for before an event.
     *
     * @param string   $event
     * @param callable $callback
     * @return $this
     */
    public function on(string $event, callable $callback): self
    {
        $this->i__dispatcher = $this->getEventDispatcher()->with($event, $callback);

        return $this;
    }

    /**
     * Trigger before an event.
     *
     * @param string $event
     * @param mixed  $payload
     * @return mixed
     */
    public function trigger(string $event, $payload = null)
    {
        return $this->getEventDispatcher()->dispatch($event, $this, $payload);
    }
}
