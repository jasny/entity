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
