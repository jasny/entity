<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractBasicEntity;
use Jasny\Entity\Event;
use Jasny\Entity\Tests\CreateEntityTrait;
use Jasny\TestHelper;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \Jasny\Entity\Traits\ToAssocTrait
 */
class ToAssocTraitTest extends TestCase
{
    use TestHelper;
    use CreateEntityTrait;

    public function testToAssoc()
    {
        $entity = $this->createBasicEntity();
        $entity->foo = 'wuz';
        $entity->bar = 23;

        $result = $entity->toAssoc();

        $this->assertSame(['foo' => 'wuz', 'bar' => 23], $result);
    }

    public function testToAssocEvent()
    {
        $entity = $this->createBasicEntity();
        $entity->foo = 'wuz';
        $entity->bar = 23;

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())->method('dispatch')
            ->with($this->isInstanceOf(Event\ToAssoc::class))
            ->willReturnCallback(function(Event\ToAssoc $event) {
                $this->assertEquals(['foo' => 'wuz', 'bar' => 23], $event->getPayload());
                $event->setPayload(['foo' => 'kuz23', 'own' => 'me']);
            });

        $entity->setEventDispatcher($dispatcher);

        $result = $entity->toAssoc();

        $this->assertSame(['foo' => 'kuz23', 'own' => 'me'], $result);
    }
}
