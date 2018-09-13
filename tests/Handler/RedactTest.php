<?php

namespace Jasny\Entity\Tests\Handler;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\Handler\Redact;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Handler\Redact
 */
class RedactTest extends TestCase
{
    public function testInvoke()
    {
        $entity = $this->createMock(EntityInterface::class);

        $handler = new Redact(['foo', 'bar', 'qux']);
        $result = $handler($entity, ['foo' => 'hello', 'bar' => 42, 'other' => 'good']);

        $this->assertEquals(['foo' => 'hello', 'bar' => 42], $result);
    }

    public function testInvokeWithObject()
    {
        $entity = $this->createMock(EntityInterface::class);

        $handler = new Redact(['foo', 'bar', 'qux']);
        $result = $handler($entity, (object)['foo' => 'hello', 'bar' => 42, 'other' => 'good']);

        $this->assertEquals((object)['foo' => 'hello', 'bar' => 42,], $result);
    }

    /**
     * @expectedException \TypeError
     */
    public function testInvokeWithNull()
    {
        $entity = $this->createMock(EntityInterface::class);

        $handler = new Redact(['foo', 'bar', 'qux']);
        $handler($entity, null);
    }
}
