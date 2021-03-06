<?php /** @noinspection PhpPropertyNamingConventionInspection */

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use Jasny\Entity\EntityInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Dispatch events emitted by the entity.
 */
trait DispatchEventTrait
{
    /**
     * @var EventDispatcherInterface
     * @ignore
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
        $this->i__dispatcher = $dispatcher;
    }

    /**
     * Get the event dispatcher
     *
     * @return EventDispatcherInterface
     * @throws \LogicException if event dispatcher isn't set
     */
    final public function getEventDispatcher(): EventDispatcherInterface
    {
        if (!isset($this->i__dispatcher)) {
            throw new \LogicException("Event dispatcher has not been set for this entity");
        }

        return $this->i__dispatcher;
    }

    /**
     * Dispatch an event.
     *
     * @param object $event
     * @return object  The event.
     *
     * @template TEvent of object
     * @phpstan-param TEvent $event
     * @phpstan-return TEvent
     */
    public function dispatchEvent(object $event): object
    {
        if (isset($this->i__dispatcher)) {
            $this->i__dispatcher->dispatch($event);
        }

        return $event;
    }
}
