<?php

namespace Jasny\Entity\Tests\EventHandler;

use Jasny\Entity\Entity;
use Jasny\Entity\EventHandler\Redact;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\EventHandler\Redact
 */
class RedactTest extends TestCase
{
    public function testInvoke()
    {
        $entity = $this->createMock(Entity::class);

        $handler = new Redact(['foo', 'bar', 'qux']);
        $result = $handler($entity, ['foo' => 'hello', 'bar' => 42, 'other' => 'good']);

        $this->assertEquals(['foo' => 'hello', 'bar' => 42], $result);
    }

    public function testInvokeWithObject()
    {
        $entity = $this->createMock(Entity::class);

        $handler = new Redact(['foo', 'bar', 'qux']);
        $result = $handler($entity, (object)['foo' => 'hello', 'bar' => 42, 'other' => 'good']);

        $this->assertEquals((object)['foo' => 'hello', 'bar' => 42,], $result);
    }

    /**
     * @expectedException \TypeError
     */
    public function testInvokeWithNull()
    {
        $entity = $this->createMock(Entity::class);

        $handler = new Redact(['foo', 'bar', 'qux']);
        $handler($entity, null);
    }
}
