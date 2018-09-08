<?php

namespace Jasny\Tests\Entity\Traits;

use JsonSerializable;
use Jasny\Entity\DynamicInterface;
use Jasny\Entity\Traits\JsonSerializeTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers Jasny\Entity\Traits\JsonSerializeTrait
 * @group entity
 */
class JsonSerializeTraitTest extends TestCase
{
    /**
     * Object implementing JsonSerializeTrait, not dynamic
     * @var object
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = $this->createObject();;
    }

    /**
     * Test 'jsonSerialize' method
     */
    public function testJsonSerialize()
    {
        $this->entity->foo = 'bar';
        $this->entity->color = 'blue';
        $this->entity->non_exist = 'zoo';
        $expected = (object)['foo' => 'bar', 'color' => 'blue', 'event' => null];

        $result = $this->entity->jsonSerialize();

        $this->assertEquals($expected, $result);
        $this->assertSame('jsonSerialize', $this->entity->event);
    }

    /**
     * Test 'jsonSerialize' method for DateTime value
     */
    public function testJsonSerializeCastDateTime()
    {
        $data = (object)['foo' => new \DateTime('2013-03-01 16:04:00 +01:00'), 'color' => 'pink'];
        $expected = (object)['foo' => '2013-03-01T16:04:00+0100', 'color' => 'pink', 'event' => null];

        $this->entity->foo = $data->foo;
        $this->entity->color = $data->color;

        $result = $this->entity->jsonSerialize();

        $this->assertEquals($expected, $result);
        $this->assertSame('jsonSerialize', $this->entity->event);
    }

    /**
     * Test 'jsonSerialize' method for serializable value
     */
    public function testJsonSerializeCastJsonSerializable()
    {
        $entity = $this->entity;
        $entity->foo = $this->getMockForAbstractClass(\JsonSerializable::class);
        $entity->foo->expects($this->once())->method('jsonSerialize')->willReturn('bar');

        $expected = (object)['foo' => 'bar', 'color' => null, 'event' => null];

        $result = $entity->jsonSerialize();

        $this->assertEquals($expected, $result);
        $this->assertSame('jsonSerialize', $entity->event);
    }

    /**
     * Test 'jsonSerialize' method for iterable value
     */
    public function testJsonSerializeIterable()
    {
        $entity = $this->entity;
        $entity->foo = new \ArrayObject(['zoo' => 'bar']);

        $expected = (object)['foo' => ['zoo' => 'bar'], 'color' => null, 'event' => null];

        $result = $entity->jsonSerialize();

        $this->assertEquals($expected, $result);
        $this->assertSame('jsonSerialize', $entity->event);
    }

    /**
     * Test 'jsonSerialize' method for dynamic entity
     */
    public function testJsonSerializeDynamic()
    {
        $entity = $this->createDynamicObject();

        $entity->foo = 'bar';
        $entity->color = 'blue';
        $expected = (object)['foo' => 'bar', 'color' => 'blue', 'event' => null];

        $result = $entity->jsonSerialize();

        $this->assertEquals($expected, $result);
        $this->assertSame('jsonSerialize', $entity->event);
    }

    /**
     * Create object, implementing JsonSerializeTrait
     *
     * @return object
     */
    protected function createObject()
    {
        return new class() {
            use JsonSerializeTrait;

            public $foo;
            public $color;
            public $event;

            public function trigger(string $event, $payload = null)
            {
                if (!isset($this->event)) {
                    $this->event = $event;
                }

                return $payload;
            }
        };
    }

    /**
     * Create object, implementing JsonSerializeTrait and DynamicInterface
     *
     * @return object
     */
    protected function createDynamicObject()
    {
        return new class() implements DynamicInterface {
            use JsonSerializeTrait;

            public $event;

            public function trigger(string $event, $payload = null)
            {
                if (!isset($this->event)) {
                    $this->event = $event;
                }

                return $payload;
            }
        };
    }
}
