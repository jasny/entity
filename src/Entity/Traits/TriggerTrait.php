<?php

namespace Jasny\Entity\Traits;

/**
 * Entity triggers implementation
 */
trait TriggerTrait
{
    /**
     * @ignore
     * @var array
     */
    private $i__triggers = [];


    /**
     * Add a trigger
     *
     * @param string $event
     * @param callable $callback
     * @return void
     */
    final protected function addTrigger(string $event, callable $callback): self
    {
        $this->i__triggers[$event][] = $callback;

        return $this;
    }

    /**
     * Get trigger for an event
     *
     * @param string $event
     * @return array
     */
    final protected function getTriggers(string $event): array
    {
        return $this->i__triggers[$event] ?? [];
    }


    /**
     * Bind a callback for before an event.
     *
     * @param string   $event
     * @param callable $callback
     * @return $this
     */
    public function on(string $event, callable $callback)
    {
        return $this->addTrigger($event, $callback);
    }

    /**
     * Trigger before an event.
     *
     * @param string $event
     * @param mixed  $payload
     * @return mixed|void
     */
    public function trigger(string $event, $payload = null)
    {
        $callbacks = $this->getTriggers($event);

        foreach ($callbacks as $callback) {
            $payload = call_user_func($callback, $this, $payload);
        }

        return $payload;
    }
}
