<?php

namespace Jasny\Entity\Tests\EventListener;

use Jasny\Entity\Entity;
use Jasny\Entity\Event;
use Jasny\Entity\EventListener\JsonCast;
use Jasny\Entity\Tests\CreateEntityTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\EventListener\JsonCast
 */
class JsonCastTest extends TestCase
{
    use CreateEntityTrait;

    /**
     * Test 'cast' method for DateTime value
     */
    public function testCastDateTime()
    {
        $entity = $this->createMock(Entity::class);
        $entity->expects($this->never())->method($this->anything());

        $data = (object)['foo' => new \DateTime('2013-03-01 16:04:00 +01:00'), 'color' => 'pink'];
        $expected = (object)['foo' => '2013-03-01T16:04:00+0100', 'color' => 'pink'];

        $event = new Event\ToJson($entity, $data);

        $listener = new JsonCast();
        $listener($event);

        $this->assertEquals($expected, $event->getPayload());
    }

    /**
     * Test 'cast' method for serializable value
     */
    public function testCastJsonSerializable()
    {
        $entity = $this->createMock(Entity::class);
        $entity->expects($this->never())->method($this->anything());

        $data = (object)['color' => null];
        $data->foo = $this->getMockForAbstractClass(\JsonSerializable::class);
        $data->foo->expects($this->once())->method('jsonSerialize')->willReturn('bar');

        $expected = (object)['foo' => 'bar', 'color' => null];

        $event = new Event\ToJson($entity, $data);

        $listener = new JsonCast();
        $listener($event);

        $this->assertEquals($expected, $event->getPayload());
    }

    /**
     * Test 'cast' method for arrayable object
     */
    public function testCastToArray()
    {
        $entity = $this->createMock(Entity::class);
        $entity->expects($this->never())->method($this->anything());

        $foo = new \SplFixedArray(2);
        $foo[0] = 'zoo';
        $foo[1] = 'two';

        $data = (object)['foo' => $foo, 'color' => null];
        $expected = (object)['foo' => ['zoo', 'two'], 'color' => null];

        $event = new Event\ToJson($entity, $data);

        $listener = new JsonCast();
        $listener($event);

        $this->assertEquals($expected, $event->getPayload());
    }

    /**
     * Test 'cast' method for ArrayObject
     */
    public function testCastGetArrayCopy()
    {
        $entity = $this->createMock(Entity::class);
        $entity->expects($this->never())->method($this->anything());

        $data = (object)['foo' => new \ArrayObject(['zoo' => 'bar', 'one' => 'two']), 'color' => null];
        $expected = (object)['foo' => ['zoo' => 'bar', 'one' => 'two'], 'color' => null];

        $event = new Event\ToJson($entity, $data);

        $listener = new JsonCast();
        $listener($event);

        $this->assertEquals($expected, $event->getPayload());
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
        $entity = $this->createMock(Entity::class);
        $entity->expects($this->never())->method($this->anything());

        $data = (object)['foo' => $this->generator(), 'color' => null];
        $expected = (object)['foo' => ['zoo' => 'bar', 'one' => 'two'], 'color' => null];

        $event = new Event\ToJson($entity, $data);

        $listener = new JsonCast();
        $listener($event);

        $this->assertEquals($expected, $event->getPayload());
    }

    /**
     * Test 'cast' method for an nesting array
     */
    public function testCastRecursive()
    {
        $entity = $this->createMock(Entity::class);
        $entity->expects($this->never())->method($this->anything());

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

        $event = new Event\ToJson($entity, $data);

        $listener = new JsonCast();
        $listener($event);

        $this->assertEquals($expected, $event->getPayload());
    }

    public function testCastNested()
    {
        $entity = $this->createNestedEntity();

        $event = new Event\ToJson($entity, $entity->jsonSerialize());

        $listener = new JsonCast();
        $listener($event);

        $expected = (object)[
            'foo' => (object)[
                'id' => 42,
                'foo' => 'Foo Foo',
            ],
            'bar' => (object)[
                'uno' => (object)[
                    'id' => 1,
                    'foo' => null,
                    'bar' => 0,
                ],
                'dos' => [
                    (object)[
                        'id' => 2,
                        'foo' => null,
                        'bar' => 0,
                    ],
                    (object)[
                        'id' => 42,
                        'foo' => 'Foo Foo',
                    ],
                ],
                'tres' => [
                    'hello',
                    (object)[
                        'id' => 1,
                        'foo' => null,
                        'bar' => 0,
                    ],
                    null,
                    'plus',
                    (object)[
                        'id' => 2,
                        'foo' => null,
                        'bar' => 0,
                    ],
                ],
                'more' => [
                    'like' => 'this',
                ],
            ],
        ];

        $this->assertEquals($expected, $event->getPayload());
    }
}
