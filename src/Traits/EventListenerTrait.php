<?php

declare(strict_types=1);

namespace Jasny\Entity\Traits;

use LogicException;
use Psr\EventDispatcher\EventDispatcherInterface;
use function Improved\type_check;
use UnexpectedValueException;

/**
 * Add an event listener to the event dispatcher.
 */
trait EventListenerTrait
{
    /**
     * Set the event dispatcher
     *
     * @param EventDispatcherInterface $dispatcher
     * @return void
     */
    abstract public function setEventDispatcher(EventDispatcherInterface $dispatcher): void;

    /**
     * Get the event dispatcher
     *
     * @return EventDispatcherInterface
     * @throws LogicException if event dispatcher isn't set
     */
    abstract public function getEventDispatcher(): EventDispatcherInterface;

    /**
     * Add an event listener to the entity's event dispatcher.
     *
     * @param callable $listener
     */
    public function addEventListener(callable $listener): void
    {
        /** @var \Jasny\EventDispatcher\EventDispatcher $dispatcher */
        $dispatcher = type_check(
            $this->getEventDispatcher(),
            'Jasny\EventDispatcher\EventDispatcher',
            new LogicException('Unsupported event dispatcher. Please overload the addEventListener method.')
        );

        /** @var \Jasny\EventDispatcher\ListenerProvider $provider */
        $provider = type_check(
            $dispatcher->getListenerProvider(),
            'Jasny\EventDispatcher\ListenerProvider',
            new UnexpectedValueException('Unsupported listener provider; expected %2s, got %1s')
        );

        $newProvider = $provider->withListener($listener);
        $newDispatcher = $dispatcher->withListenerProvider($newProvider);

        $this->setEventDispatcher($newDispatcher);
    }
}
