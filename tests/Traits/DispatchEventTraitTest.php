<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractEntity;
use Jasny\Entity\Tests\CreateEntityTrait;
use LogicException;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \Jasny\Entity\Traits\DispatchEventTrait
 */
class DispatchEventTraitTest extends TestCase
{
    use CreateEntityTrait;

    public function testGetEventDispatcher()
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->never())->method($this->anything());

        $entity = $this->createBasicEntity();
        $entity->setEventDispatcher($dispatcher);

        $this->assertSame($dispatcher, $entity->getEventDispatcher());
    }

    /**
     * @expectedException LogicException
     */
    public function testGetEventDispatcherNotSet()
    {
        $entity = $this->createBasicEntity();
        $entity->getEventDispatcher();
    }

    public function testDispatchEvent()
    {
        $event1 = (object)[];
        $event2 = (object)[];

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->exactly(2))->method('dispatch')
            ->withConsecutive([$event1], [$event2]);

        $entity = $this->createBasicEntity();
        $entity->setEventDispatcher($dispatcher);

        $entity->dispatchEvent($event1);
        $entity->dispatchEvent($event2);
    }
}
