<?php

declare(strict_types=1);

namespace Jasny\Entity\Tests;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\Event\AfterSet;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Event\AbstractEvent
 */
class EventTest extends TestCase
{
    public function testGetEntity()
    {
        $entity = $this->createMock(EntityInterface::class);
        $event = new AfterSet($entity);

        $this->assertSame($entity, $event->getEntity());
    }

    public function testPayload()
    {
        $payload = (object)['foo' => 'bar'];

        $entity = $this->createMock(EntityInterface::class);
        $event = new AfterSet($entity, $payload);

        $this->assertSame($payload, $event->getPayload());

        $event->setPayload('hello');
        $this->assertEquals('hello', $event->getPayload());
    }
}
