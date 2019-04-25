<?php

namespace Jasny\Entity\Tests\EventListener;

use Jasny\Entity\Entity;
use Jasny\Entity\Event\ToJson;
use Jasny\Entity\EventListener\JsonCast;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\EventListener\JsonCast
 */
class JsonCastTest extends TestCase
{
    /**
     * Test 'cast' method for DateTime value
     */
    public function testCastDateTime()
    {
        $entity = $this->createMock(Entity::class);

        $data = (object)['foo' => new \DateTime('2013-03-01 16:04:00 +01:00'), 'color' => 'pink'];
        $expected = (object)['foo' => '2013-03-01T16:04:00+0100', 'color' => 'pink'];

        $handler = new JsonCast();
        $result = $handler(new ToJson($entity, $data));

        $this->assertEquals($expected, $result);
    }

    /**
     * Test 'cast' method for serializable value
     */
    public function testCastJsonSerializable()
    {
        $entity = $this->createMock(Entity::class);

        $data = (object)['color' => null];
        $data->foo = $this->getMockForAbstractClass(\JsonSerializable::class);
        $data->foo->expects($this->once())->method('jsonSerialize')->willReturn('bar');

        $expected = (object)['foo' => 'bar', 'color' => null];

        $handler = new JsonCast();
        $result = $handler(new ToJson($entity, $data));

        $this->assertEquals($expected, $result);
    }

    /**
     * Test 'cast' method for arrayable object
     */
    public function testCastToArray()
    {
        $entity = $this->createMock(Entity::class);

        $foo = new \SplFixedArray(2);
        $foo[0] = 'zoo';
        $foo[1] = 'two';

        $data = (object)['foo' => $foo, 'color' => null];
        $expected = (object)['foo' => ['zoo', 'two'], 'color' => null];

        $handler = new JsonCast();
        $result = $handler(new ToJson($entity, $data));

        $this->assertEquals($expected, $result);
    }

    /**
     * Test 'cast' method for ArrayObject
     */
    public function testCastGetArrayCopy()
    {
        $this->markTestIncomplete();

        $entity = $this->createMock(Entity::class);

        $data = (object)['foo' => new \ArrayObject(['zoo' => 'bar', 'one' => 'two']), 'color' => null];
        $expected = (object)['foo' => ['zoo' => 'bar', 'one' => 'two'], 'color' => null];

        $handler = new JsonCast();
        $result = $handler(new ToJson($entity, $data));

        $this->assertEquals($expected, $result);
    }

    protected function generator(): \Generator
    {
        foreach (['zoo' => 'bar', 'one' => 'two'] as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * Test 'cast' method for Traversable
     */
    public function testCastTraversable()
    {
        $this->markTestIncomplete();

        $entity = $this->createMock(Entity::class);

        $data = (object)['foo' => $this->generator(), 'color' => null];
        $expected = (object)['foo' => ['zoo' => 'bar', 'one' => 'two'], 'color' => null];

        $handler = new JsonCast();
        $result = $handler(new ToJson($entity, $data));

        $this->assertEquals($expected, $result);
    }

    /**
     * Test 'cast' method for an nesting array
     */
    public function testCastRecursive()
    {
        $this->markTestIncomplete();

        $entity = $this->createMock(Entity::class);

        $foo = [
            'zoo' => 'bar',
            'now' => new \DateTime('2013-03-01 16:04:00 +01:00'),
            'sub' => [
                'good',
                [
                    new class() implements \JsonSerializable {
                        public function jsonSerialize()
                        {
                            return 'here';
                        }
                    }
                ]
            ]
        ];

        $data = (object)['foo' => $foo, 'color' => null];
        $expected = (object)[
            'foo' => [
                'zoo' => 'bar',
                'now' => '2013-03-01T16:04:00+0100',
                'sub' => [
                    'good',
                    [
                        'here'
                    ]
                ]
            ],
            'color' => null
        ];

        $handler = new JsonCast();
        $result = $handler(new ToJson($entity, $data));

        $this->assertEquals($expected, $result);
    }


    /**
     * @expectedException \TypeError
     */
    public function testInvokeWithNull()
    {
        $entity = $this->createMock(Entity::class);

        $handler = new JsonCast();
        $handler($entity, null);
    }
}
