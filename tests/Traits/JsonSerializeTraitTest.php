<?php

namespace Jasny\Entity\Tests\Traits;

use Jasny\Entity\AbstractBasicEntity;
use Jasny\Entity\DynamicEntityInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\JsonSerializeTrait
 */
class JsonSerializeTraitTest extends TestCase
{
    /**
     * @var AbstractBasicEntity
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = new class() extends AbstractBasicEntity {
            public $foo;
            public $color;

            protected $event;

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
     * Test 'jsonSerialize' method
     */
    public function testJsonSerialize()
    {
        $this->entity->foo = 'bar';
        $this->entity->color = 'blue';
        $this->entity->non_exist = 'zoo';
        $expected = (object)['foo' => 'bar', 'color' => 'blue'];

        $result = $this->entity->jsonSerialize();

        $this->assertEquals($expected, $result);

        $this->assertAttributeEquals('jsonSerialize', 'event', $this->entity);
    }

    /**
     * Test 'jsonSerialize' method for DateTime value
     */
    public function testJsonSerializeCastDateTime()
    {
        $data = (object)['foo' => new \DateTime('2013-03-01 16:04:00 +01:00'), 'color' => 'pink'];
        $expected = (object)['foo' => '2013-03-01T16:04:00+0100', 'color' => 'pink'];

        $this->entity->foo = $data->foo;
        $this->entity->color = $data->color;

        $result = $this->entity->jsonSerialize();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test 'jsonSerialize' method for serializable value
     */
    public function testJsonSerializeCastJsonSerializable()
    {
        $this->entity->foo = $this->getMockForAbstractClass(\JsonSerializable::class);
        $this->entity->foo->expects($this->once())->method('jsonSerialize')->willReturn('bar');

        $expected = (object)['foo' => 'bar', 'color' => null];

        $result = $this->entity->jsonSerialize();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test 'jsonSerialize' method for iterable value
     */
    public function testJsonSerializeIterable()
    {
        $entity = $this->entity;
        $entity->foo = new \ArrayObject(['zoo' => 'bar']);

        $expected = (object)['foo' => ['zoo' => 'bar'], 'color' => null];

        $result = $entity->jsonSerialize();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test 'jsonSerialize' method for dynamic entity
     */
    public function testJsonSerializeDynamic()
    {
        $this->entity = new class() extends AbstractBasicEntity implements DynamicEntityInterface {
        };

        $this->entity->foo = 'bar';
        $this->entity->color = 'blue';
        $expected = (object)['foo' => 'bar', 'color' => 'blue'];

        $result = $this->entity->jsonSerialize();

        $this->assertEquals($expected, $result);
    }
}
