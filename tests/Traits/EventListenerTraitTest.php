<?php

declare(strict_types=1);

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\Tests\CreateEntityTrait;
use Jasny\EventDispatcher\EventDispatcher;
use Jasny\EventDispatcher\ListenerProvider;
use Jasny\TestHelper;
use LogicException;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use UnexpectedValueException;

/**
 * @covers \Jasny\Entity\Traits\EventListenerTrait
 */
class EventListenerTraitTest extends TestCase
{
    use CreateEntityTrait;

    public function testAddEventListener()
    {
        $event = (object)[];
        $listener = function() {};

        $newProvider = $this->createMock(ListenerProvider::class);

        $newDispatcher = $this->createMock(EventDispatcher::class);
        $newDispatcher->expects($this->once())->method('dispatch')->with($this->identicalTo($event));

        $provider = $this->createMock(ListenerProvider::class);
        $provider->expects($this->once())->method('withListener')
            ->with($this->identicalTo($listener))
            ->wilLReturn($newProvider);

        $dispatcher = $this->createMock(EventDispatcher::class);
        $dispatcher->expects($this->once())->method('getListenerProvider')
            ->willReturn($provider);
        $dispatcher->expects($this->once())->method('withListenerProvider')
            ->with($this->identicalTo($newProvider))
            ->willReturn($newDispatcher);

        $entity = $this->createBasicEntity();
        $entity->setEventDispatcher($dispatcher);

        $entity->addEventListener($listener);

        $entity->dispatchEvent($event);
    }

    /**
     * @expectedException LogicException
     */
    public function testForUnknownDispatcher()
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $entity = $this->createBasicEntity();
        $entity->setEventDispatcher($dispatcher);

        $listener = function() {};
        $entity->addEventListener($listener);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testForUnknownProvider()
    {
        $listener = function() {};

        $provider = $this->createMock(ListenerProviderInterface::class);

        $dispatcher = $this->createMock(EventDispatcher::class);
        $dispatcher->expects($this->once())->method('getListenerProvider')
            ->willReturn($provider);

        $entity = $this->createBasicEntity();
        $entity->setEventDispatcher($dispatcher);

        $entity->addEventListener($listener);
    }
}
