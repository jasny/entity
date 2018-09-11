<?php

namespace Jasny\Tests\Entity\Traits;

use Jasny\Entity\DynamicInterface;
use Jasny\Entity\Traits\GetSetTrait;
use Jasny\Entity\EntityInterface;
use Jasny\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jasny\Entity\Traits\GetSetTrait
 */
class GetSetTraitTest extends TestCase
{
    use TestHelper;

    protected function createObject()
    {
        return new class() {
            use GetSetTrait;

            public $foo;
            public $num = 0;

            public function trigger($event, $payload) {
                return $payload;
            }
        };
    }

    protected function createDynamicObject()
    {
        return new class() implements DynamicInterface {
            use GetSetTrait;

            public $foo;
            public $num = 0;

            public function trigger($event, $payload) {
                return $payload;
            }
        };
    }

    protected function createObjectWithTrigger(callable $trigger)
    {
        return new class($trigger) {
            use GetSetTrait;

            private $trigger;

            public function __construct(callable $trigger) {
                $this->trigger = $trigger;
            }

            public function trigger() {
                $args = func_get_args();
                return call_user_func_array($this->trigger, $args);
            }
        };
    }


    /**
     * Test 'set' method for single value
     */
    public function testSetValue()
    {
        $object = $this->createObject();
        $object->set('foo', 'bar');

        $this->assertAttributeSame('bar', 'foo', $object);
        $this->assertAttributeSame(0, 'num', $object);
    }

    /**
     * Test 'set' method with null value
     */
    public function testSetValueToNull()
    {
        $object = $this->createObject();
        $object->set('num', null);

        $this->assertAttributeSame(null, 'foo', $object);
        $this->assertAttributeSame(null, 'num', $object);
    }

    /**
     * Test 'set' method for multiple values
     */
    public function testSetValues()
    {
        $object = $this->createObject();
        $object->set(['foo' => 'bar', 'num' => 100, 'dyn' => 'woof']);

        $this->assertAttributeSame('bar', 'foo', $object);
        $this->assertAttributeSame(100, 'num', $object);
        $this->assertObjectNotHasAttribute('dyn', $object);
    }

    /**
     * Test 'set' method for multiple values and dynamic entity
     */
    public function testSetValuesDynamic()
    {
        $object = $this->createDynamicObject();
        $object->set(['foo' => 'bar', 'num' => 100, 'dyn' => 'woof']);

        $this->assertAttributeSame('bar', 'foo', $object);
        $this->assertAttributeSame(100, 'num', $object);
        $this->assertAttributeSame('woof', 'dyn', $object);
    }

    /**
     * Test 'set' method with invalid argument
     *
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage if first argument is a string, a second argument is required
     */
    public function testSetValueInvalidArgument()
    {
        $object = $this->createDynamicObject();
        $object->set('foo');
    }

    /**
     * Test 'toAssoc' method
     */
    public function testToAssoc()
    {
        $expected = ['foo' => 'bar', 'num' => 23];

        $object = $this->createDynamicObject();

        $object->foo = 'bar';
        $object->num = 23;

        $result = $object->toAssoc();

        $this->assertSame($expected, $result);
    }
}
