<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\Tests\_Support\CreateEntityTrait;
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

    public function testGetEventDispatcherNotSet()
    {
        $entity = $this->createBasicEntity();

        $this->expectException(\LogicException::class);

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
