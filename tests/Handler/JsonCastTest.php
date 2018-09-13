<?php

namespace Jasny\Entity\Tests\Handler;

use Jasny\Entity\EntityInterface;
use Jasny\Entity\Handler\JsonCast;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Handler\JsonCast
 */
class JsonCastTest extends TestCase
{
    /**
     * Test 'cast' method for DateTime value
     */
    public function testCastDateTime()
    {
        $entity = $this->createMock(EntityInterface::class);

        $data = (object)['foo' => new \DateTime('2013-03-01 16:04:00 +01:00'), 'color' => 'pink'];
        $expected = (object)['foo' => '2013-03-01T16:04:00+0100', 'color' => 'pink'];

        $handler = new JsonCast();
        $result = $handler($entity, $data);

        $this->assertEquals($expected, $result);
    }

    /**
     * Test 'cast' method for serializable value
     */
    public function testCastJsonSerializable()
    {
        $entity = $this->createMock(EntityInterface::class);

        $data = (object)['color' => null];
        $data->foo = $this->getMockForAbstractClass(\JsonSerializable::class);
        $data->foo->expects($this->once())->method('jsonSerialize')->willReturn('bar');

        $expected = (object)['foo' => 'bar', 'color' => null];

        $handler = new JsonCast();
        $result = $handler($entity, $data);

        $this->assertEquals($expected, $result);
    }

    /**
     * Test 'cast' method for iterable value
     */
    public function testJsonSerializeIterable()
    {
        $entity = $this->createMock(EntityInterface::class);

        $data = (object)['foo' => new \ArrayObject(['zoo' => 'bar']), 'color' => null];
        $expected = (object)['foo' => ['zoo' => 'bar'], 'color' => null];

        $handler = new JsonCast();
        $result = $handler($entity, $data);

        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \TypeError
     */
    public function testInvokeWithNull()
    {
        $entity = $this->createMock(EntityInterface::class);

        $handler = new JsonCast();
        $handler($entity, null);
    }
}
